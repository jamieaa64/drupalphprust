#!/usr/bin/env python3
"""HTTP regression; prints no session IDs or cookie values."""
import http.cookiejar, json, sys, urllib.request
base = sys.argv[1]
jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
def request(path):
    with opener.open(base + path, timeout=30) as response:
        return json.load(response), response.headers.get_all('Set-Cookie', [])
# Rotation immediately after starting a new session must replace its pending cookie.
data, headers = request('?rotate=1&twice=1')
assert data == {'rotated': True}, data
assert sum(h.startswith('OOBREGRESSION=') for h in headers) == 1, 'duplicate session cookies'
assert any(h.startswith('unrelated=') for h in headers), 'unrelated cookie removed'
assert any('HttpOnly' in h and 'SameSite=Lax' in h for h in headers if h.startswith('OOBREGRESSION='))
assert request('')[0] == {'marker':'persisted-after-rotation'}, 'rotated session not resumed'
# Rotation of an existing cookie must also send the replacement.
old = next(c.value for c in jar if c.name == 'OOBREGRESSION')
assert request('?rotate=1')[0] == {'rotated': True}
new = next(c.value for c in jar if c.name == 'OOBREGRESSION')
assert old != new, 'browser retained the old session ID'
assert request('')[0] == {'marker':'persisted-after-rotation'}
_, headers = request('?rotate=1&no_cookie=1')
assert all(not h.startswith('OOBREGRESSION=') for h in headers), 'cookies disabled but session cookie emitted'
print('HTTP session rotation: PASS')
