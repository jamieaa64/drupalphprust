--TEST--
XMLWriter memory API: xmlwriter-errors (PHP 8.4.27 / libxml 2.9.14 oracle)
--FILE--
<?php
// Warnings must be PHP diagnostics and remain catchable by user error handlers.
$writer = new XMLWriter();
$writer->openMemory();
set_error_handler(function ($severity, $message) { echo "warning: $message\n"; return true; });
var_dump($writer->startDocument('1.0', 'not-a-real-encoding'));
restore_error_handler();
$writer->openMemory();
set_error_handler(function ($severity, $message) { throw new Exception($message); });
try { $writer->startDocument('1.0', 'not-a-real-encoding'); echo "unexpected\n"; }
catch (Exception $e) { echo 'caught: ', $e->getMessage(), "\n"; }
restore_error_handler();
$writer->openMemory();
foreach (['startPi', 'writePi', 'startAttribute', 'writeAttribute', 'startElementNs'] as $method) {
    try {
        if ($method === 'startElementNs') $writer->$method('p', 'bad name', 'urn:x');
        elseif (str_starts_with($method, 'write')) $writer->$method('bad name', 'x');
        else $writer->$method('bad name');
    } catch (ValueError $e) { echo $e->getMessage(), "\n"; }
}
$writer->writeElement('after', 'handler restored');
echo $writer->outputMemory(), "\n";
// Inherited constructor and clone prohibition cannot be bypassed by a subclass.
class ChildWriter extends XMLWriter { public function __construct() {} public function __clone() { echo "unexpected clone\n"; } }
$child = new ChildWriter(); $child->openMemory();
try { $copy = clone $child; } catch (Error $e) { echo $e->getMessage(), "\n"; }

libxml_use_internal_errors(true);
libxml_clear_errors();
set_error_handler(function () { echo "unexpected internal warning\n"; return true; });
$writer->openMemory();
var_dump($writer->startDocument('1.0', 'not-a-real-encoding'));
foreach (libxml_get_errors() as $error) {
    var_dump($error->level, $error->code, $error->line, $error->column, $error->file, $error->message);
}
restore_error_handler();
libxml_clear_errors();
libxml_use_internal_errors(false);

foreach ([false, true] as $internal) {
    libxml_use_internal_errors($internal);
    libxml_clear_errors();
    $writer->openMemory();
    $writer->startDocument('1.0', 'ISO-8859-1');
    var_dump($writer->startElement('r'), $writer->text("\xff"), $writer->outputMemory());
    foreach (libxml_get_errors() as $error) {
        var_dump($error->level, $error->code, $error->message);
    }
}
libxml_clear_errors();
libxml_use_internal_errors(false);
--EXPECT--
warning: XMLWriter::startDocument(): xmlTextWriterStartDocument : unsupported encoding
bool(false)
caught: XMLWriter::startDocument(): xmlTextWriterStartDocument : unsupported encoding
XMLWriter::startPi(): Argument #2 must be a valid PI target, "bad name" given
XMLWriter::writePi(): Argument #2 ($content) must be a valid PI target, "bad name" given
XMLWriter::startAttribute(): Argument #2 must be a valid attribute name, "bad name" given
XMLWriter::writeAttribute(): Argument #2 ($value) must be a valid attribute name, "bad name" given
XMLWriter::startElementNs(): Argument #3 ($namespace) must be a valid element name, "bad name" given
<after>handler restored</after>
Trying to clone an uncloneable object of class ChildWriter
bool(false)
int(3)
int(32)
int(0)
int(0)
string(0) ""
string(50) "xmlTextWriterStartDocument : unsupported encoding
"
bool(true)
bool(true)
string(0) ""
bool(true)
bool(true)
string(0) ""
int(2)
int(1544)
string(13) "encoder error"
