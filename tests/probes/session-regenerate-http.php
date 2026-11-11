<?php
session_name('OOBREGRESSION');
session_set_cookie_params(['path'=>'/', 'httponly'=>true, 'samesite'=>'Lax']);
if (isset($_GET['no_cookie'])) ini_set('session.use_cookies', '0');
session_start();
$before = session_id();
if (isset($_GET['rotate'])) {
    $_SESSION['marker'] = 'persisted-after-rotation';
    setcookie('unrelated', 'preserved', ['path'=>'/']);
    session_regenerate_id(true);
    if (isset($_GET['twice'])) session_regenerate_id(true);
    echo json_encode(['rotated'=>session_id()!==$before]);
} else {
    echo json_encode(['marker'=>$_SESSION['marker'] ?? null]);
}
