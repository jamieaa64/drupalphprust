<?php
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
$data = ['keep' => ['a' => 1, 'b' => 2, 'nested' => ['c' => 3]], 'skip' => ['hidden' => 4], 'z' => 5];
$callback = function ($value, $key, $inner) {
    if (!$inner instanceof RecursiveIterator) { throw new RuntimeException('Callback received wrong iterator'); }
    return $inner->hasChildren() ? $key !== 'skip' : $value % 2 === 1;
};
$it = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator($data), $callback);
emit('inheritance', [$it instanceof CallbackFilterIterator, $it instanceof RecursiveIterator]);
foreach ([RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CHILD_FIRST] as $mode) {
    $out = [];
    foreach (new RecursiveIteratorIterator($it, $mode) as $k => $v) { $out[] = [$k, is_array($v) ? 'branch' : $v]; }
    emit('walk', $out);
}
$it->rewind();
emit('inner', get_class($it->getInnerIterator()));
emit('children', [$it->hasChildren(), get_class($it->getChildren()), iterator_to_array($it->getChildren())]);
class ProbeChild extends RecursiveCallbackFilterIterator {}
$sub = new ProbeChild(new RecursiveArrayIterator([[1,2]]), $callback);
$sub->rewind(); emit('subclass', get_class($sub->getChildren()));
$empty = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([]), $callback);
emit('empty', iterator_to_array($empty));
$allRejected = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator($data), fn() => false);
emit('rejected', iterator_to_array(new RecursiveIteratorIterator($allRejected)));
try { new RecursiveCallbackFilterIterator(new ArrayIterator([]), $callback); } catch (TypeError $e) { emit('bad_iterator', get_class($e)); }
try { new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([]), 42); } catch (TypeError $e) { emit('bad_callback', get_class($e)); }
$throws = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([1]), function () { throw new RuntimeException('callback failure'); });
try { $throws->rewind(); } catch (RuntimeException $e) { emit('exception', $e->getMessage()); }
$root = sys_get_temp_dir() . '/phpr-iterator-' . getmypid();
mkdir($root); mkdir($root . '/keep'); mkdir($root . '/skip');
file_put_contents($root . '/keep/one.info.yml', 'probe');
file_put_contents($root . '/keep/no.txt', 'probe');
file_put_contents($root . '/keep/two.info.yml', 'probe');
file_put_contents($root . '/skip/hidden.info.yml', 'probe');
try {
    $dir = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS);
    $filter = new RecursiveCallbackFilterIterator($dir, function ($file, $key, $inner) {
        return $inner->hasChildren() ? $file->getFilename() !== 'skip' : str_ends_with($file->getFilename(), '.info.yml');
    });
    $selfDir = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_SELF);
    $selfFilter = new RecursiveCallbackFilterIterator($selfDir, function (RecursiveDirectoryIterator $current, $key, $inner) {
        if ($current !== $inner) { throw new RuntimeException('CURRENT_AS_SELF lost identity'); }
        return $current->getFilename() === 'keep';
    });
    $selfFilter->rewind();
    emit('self', [$selfFilter->current() === $selfDir, $selfFilter->current()->getFilename()]);
    $child = $selfFilter->getChildren()->getInnerIterator();
    emit('child_self', $child->current() === $child);
    $selfWalk = new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_SELF), function (RecursiveDirectoryIterator $file) {
        return $file->hasChildren() ? $file->getFilename() !== 'skip' : str_ends_with($file->getFilename(), '.info.yml');
    });
    $names = [];
    foreach (new RecursiveIteratorIterator($selfWalk) as $entry) { $names[] = $entry->getSubPathname(); }
    sort($names); emit('self_walk', $names);
    $forward = new RecursiveIteratorIterator($selfWalk);
    $forward->rewind();
    emit('forwarded_subpath', $forward->getSubPath());
    $files = [];
    foreach (new RecursiveIteratorIterator($filter) as $file) { $files[] = $file->getFilename(); }
    sort($files); emit('directory', $files);
} finally {
    unlink($root . '/keep/two.info.yml');
    unlink($root . '/keep/one.info.yml'); unlink($root . '/keep/no.txt'); unlink($root . '/skip/hidden.info.yml');
    rmdir($root . '/keep'); rmdir($root . '/skip'); rmdir($root);
}

foreach ([0, 1, 2] as $mode) {
    $walk = new RecursiveIteratorIterator(new RecursiveArrayIterator(['a' => [1, [2]], 'b' => 3]), $mode);
    $walk->setMaxDepth(1); $rows = [];
    foreach ($walk as $key => $value) { $rows[] = [$key, $value, $walk->getDepth()]; }
    emit('depth_limit', $rows);
}
class BrokenChildren extends RecursiveArrayIterator {
    public function getChildren(): ?RecursiveArrayIterator { throw new RuntimeException('inaccessible'); }
}
foreach ([0, 1, 2] as $mode) {
    $rows = [];
    foreach (new RecursiveIteratorIterator(new BrokenChildren([[1], 2]), $mode, RecursiveIteratorIterator::CATCH_GET_CHILD) as $v) { $rows[] = $v; }
    emit('catch_child', $rows);
}

class ErrorChildren extends RecursiveArrayIterator {
    public function getChildren(): ?RecursiveArrayIterator { throw new Error('inaccessible'); }
}
emit('catch_error', iterator_to_array(new RecursiveIteratorIterator(new ErrorChildren([[1], 2]), 0, RecursiveIteratorIterator::CATCH_GET_CHILD), false));

class ErrorHasChildren extends RecursiveArrayIterator {
    public function hasChildren(): bool { throw new Error('inaccessible'); }
}
foreach ([0, 1, 2] as $mode) {
    emit('catch_has_children', iterator_to_array(new RecursiveIteratorIterator(new ErrorHasChildren([[1], 2]), $mode, RecursiveIteratorIterator::CATCH_GET_CHILD), false));
}

$levels = new RecursiveIteratorIterator(new RecursiveArrayIterator([[1], 2]));
emit('sub_initial', [get_class($levels->getSubIterator()), $levels->getSubIterator(-1), $levels->getSubIterator(1)]);
$rows = [];
foreach ($levels as $value) { $rows[] = [$value, $levels->getDepth(), $levels->getSubIterator() === $levels->getInnerIterator(), $levels->getSubIterator(9)]; }
emit('sub_levels', $rows);
emit('sub_end', [$levels->getSubIterator(0) === $levels->getInnerIterator(), $levels->getSubIterator(1)]);
