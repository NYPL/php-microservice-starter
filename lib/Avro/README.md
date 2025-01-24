This is a fork of https://github.com/nealio82/avro-php, version 0.1.3. Changes were made to allow schema arrays to have integer keys.

1. **Datum/IODatumWriter.php/write_record() (line 158)** - Patched to supply a default value when a field isn't set.
```
$value = isset($datum[$field->name()]) ? $datum[$field->name()] : $field->default_value();
```
2. **ID/StringIO.php/write() (line 57)** - Patched to allow integer write arguments.
```
if (is_string($arg) || is_int($arg))
```
3. **Schema/Schema.php/is_valid_datum() (line 378)** - Patched to allow integer array keys.
```
if ((!is_string($k) && !is_int($k))
```

