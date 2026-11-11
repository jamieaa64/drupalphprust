#!/usr/bin/env python3
"""An idle preconnect must not block a second socket's real request."""
import argparse
import http.client
import socket
p=argparse.ArgumentParser(description=__doc__)
p.add_argument('--host',default='127.0.0.1')
p.add_argument('--port',type=int,default=18090)
p.add_argument('--path',default='/themes/contrib/mercury/build/main.min.css')
a=p.parse_args()
with socket.create_connection((a.host,a.port),timeout=5) as idle:
    client=http.client.HTTPConnection(a.host,a.port,timeout=5)
    try:
        client.request('GET',a.path)
        response=client.getresponse()
        body=response.read()
        assert response.status==200,response.status
        assert len(body)>100,'Missing static body'
    finally:
        client.close()
print('Idle preconnect regression: PASS')
