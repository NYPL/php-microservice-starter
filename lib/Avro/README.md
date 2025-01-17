This is a fork of https://github.com/nealio82/avro-php, version 0.1.3. Changes were made to allow schema arrays to have integer keys.

Datum/IODatumWriter.php/write_record() - Patched to supply a default value when a field isn't set.
ID/StringIO.php/write() - Patched to allow integer write arguments.
Schema/Schema.php/is_valid_datum() - Patched to allow integer array keys.

