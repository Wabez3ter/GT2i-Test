<?php
$MagicParser_copyright = "Copyright (c) 2005-2011 IAAI Software, All Rights Reserved";
global $MagicParser_xml_name;
global $MagicParser_xml_path;
global $MagicParser_xml_depth;
global $MagicParser_xml_length;
global $MagicParser_xml_current_record;
global $MagicParser_xml_current_key;
global $MagicParser_xml_record_path;
global $MagicParser_xml_record_path_len;
global $MagicParser_xml_done;

function MagicParser_xml_startTag($parser, $name, $attribs)
{
    global $MagicParser_xml_name;
    global $MagicParser_xml_path;
    global $MagicParser_xml_depth;
    global $MagicParser_xml_length;
    global $MagicParser_xml_current_record;
    global $MagicParser_xml_current_key;
    global $MagicParser_xml_record_path;
    global $MagicParser_xml_record_path_len;
    $MagicParser_xml_name = $name;
    $MagicParser_xml_depth++;
    $MagicParser_xml_length[$MagicParser_xml_depth] = strlen($MagicParser_xml_path);
    $MagicParser_xml_path .= $name . "/";
    if ($MagicParser_xml_path == $MagicParser_xml_record_path) {
        $MagicParser_xml_current_record = array();
        $MagicParser_xml_current_key = $MagicParser_xml_name;
    } else {
        $MagicParser_xml_current_key = substr($MagicParser_xml_path, $MagicParser_xml_record_path_len, -1);
    }

    if ($MagicParser_xml_current_key) {
        $key = $MagicParser_xml_current_key;
        $ofs = 0;
        while (isset($MagicParser_xml_current_record[$MagicParser_xml_current_key])) {
            $ofs++;
            $MagicParser_xml_current_key = $key . "@" . $ofs;
        }

        $MagicParser_xml_current_record[$MagicParser_xml_current_key] = "";

        if (is_array($attribs)) {
            foreach ($attribs as $attrib => $value) {
                $MagicParser_xml_current_record[$MagicParser_xml_current_key . "-" . $attrib] = $value;
            }
        }
    }
}

function MagicParser_xml_cdata($parser, $cdata)
{
    global $MagicParser_xml_name;
    global $MagicParser_xml_path;
    global $MagicParser_xml_current_record;
    global $MagicParser_xml_current_key;
    global $MagicParser_xml_record_path;
    global $MagicParser_xml_record_path_len;

    if (is_array($MagicParser_xml_current_record)) {
        if ($MagicParser_xml_current_key) {
            $MagicParser_xml_current_record[$MagicParser_xml_current_key] .= $cdata;
        }
    }
}

function MagicParser_xml_endTag($parser, $name)
{
    global $MagicParser_xml_path;
    global $MagicParser_xml_depth;
    global $MagicParser_xml_length;
    global $MagicParser_xml_current_record;
    global $MagicParser_xml_current_key;
    global $MagicParser_xml_record_path;
    global $MagicParser_xml_done;
    global $MagicParser_record_handler;

    if (($MagicParser_xml_path == $MagicParser_xml_record_path) && !$MagicParser_xml_done) {
        $MagicParser_xml_done = $MagicParser_record_handler($MagicParser_xml_current_record);
        unset($MagicParser_xml_current_record);
    }

    $MagicParser_xml_current_key = "";
    $MagicParser_xml_path = substr($MagicParser_xml_path, 0, $MagicParser_xml_length[$MagicParser_xml_depth]);
    $MagicParser_xml_depth--;
}

function MagicParser_xml_parse()
{
    global $MagicParser_format_data;
    global $MagicParser_error_message;
    global $MagicParser_filename;
    global $MagicParser_xml_path;
    global $MagicParser_xml_depth;
    global $MagicParser_xml_length;
    global $MagicParser_xml_current_record;
    global $MagicParser_xml_current_key;
    global $MagicParser_xml_record_path;
    global $MagicParser_xml_record_path_len;
    global $MagicParser_xml_done;
    $parser = @xml_parser_create();

    if (!$parser) {
        $MagicParser_error_message = "call to xml_parser_create() failed";
        return false;
    }

    xml_set_element_handler($parser, "MagicParser_xml_startTag", "MagicParser_xml_endTag");
    xml_set_character_data_handler($parser, "MagicParser_xml_cdata");
    $MagicParser_xml_path = "";
    $MagicParser_xml_depth = 0;
    $MagicParser_xml_length = array();
    $MagicParser_xml_current_record = array();
    $MagicParser_xml_current_key = "";
    $MagicParser_xml_record_path = $MagicParser_format_data[1];
    $MagicParser_xml_record_path_len = strlen($MagicParser_xml_record_path);
    $MagicParser_xml_done = 0;
    $fp = MagicParser_fopen($MagicParser_filename, "r");

    if (!$fp) {
        $MagicParser_error_message = "could not open " . $MagicParser_filename;
        return false;
    }
    $first = true;
    while (!MagicParser_feof($fp) && !$MagicParser_xml_done) {
        $xml = MagicParser_fread($fp, 2048);
        if ($first) {
            $xml = ltrim($xml);
            $first = false;
        }
        xml_parse($parser, $xml, false);
    }
    if (MagicParser_feof($fp)) {
        xml_parse($parser, "", true);
    }
    MagicParser_fclose($fp);
    return true;
}

global $MagicParser_xml_analysis_last_path;
global $MagicParser_xml_analysis_path;
global $MagicParser_xml_analysis_depth;
global $MagicParser_xml_analysis_length;
global $MagicParser_xml_analysis_records;
function MagicParser_xml_analysis_startTag($parser, $name, $attribs)
{
    global $MagicParser_xml_analysis_last_path;
    global $MagicParser_xml_analysis_path;
    global $MagicParser_xml_analysis_depth;
    global $MagicParser_xml_analysis_length;
    global $MagicParser_xml_analysis_records;
    $MagicParser_xml_analysis_depth++;
    $MagicParser_xml_analysis_length[$MagicParser_xml_analysis_depth] = strlen($MagicParser_xml_analysis_path);
    $MagicParser_xml_analysis_path .= $name . "/";
    if (!isset($MagicParser_xml_analysis_records[$MagicParser_xml_analysis_path])) {
        $MagicParser_xml_analysis_records[$MagicParser_xml_analysis_path]["count"] = 1;
    }
    if ($MagicParser_xml_analysis_path == $MagicParser_xml_analysis_last_path) {
        $MagicParser_xml_analysis_records[$MagicParser_xml_analysis_path]["count"]++;
    }
}

function MagicParser_xml_analysis_cdata($parser, $name)
{
}

function MagicParser_xml_analysis_endTag($parser, $name)
{
    global $MagicParser_xml_analysis_last_path;
    global $MagicParser_xml_analysis_path;
    global $MagicParser_xml_analysis_depth;
    global $MagicParser_xml_analysis_length;
    $MagicParser_xml_analysis_last_path = $MagicParser_xml_analysis_path;
    $MagicParser_xml_analysis_path = substr($MagicParser_xml_analysis_path, 0, $MagicParser_xml_analysis_length[$MagicParser_xml_analysis_depth]);
    $MagicParser_xml_analysis_depth--;
}

function MagicParser_xml_analysis()
{
    global $MagicParser_xml_analysis_last_path;
    global $MagicParser_xml_analysis_path;
    global $MagicParser_xml_analysis_depth;
    global $MagicParser_xml_analysis_length;
    global $MagicParser_xml_analysis_records;
    global $MagicParser_error_message;
    global $MagicParser_filename;
    $MagicParser_xml_analysis_last_path = "";
    $MagicParser_xml_analysis_path = "";
    $MagicParser_xml_analysis_depth = 0;
    $MagicParser_xml_analysis_length = array();
    $MagicParser_xml_analysis_records = array();
    $parser = @xml_parser_create();
    if (!$parser) {
        $MagicParser_error_message = "call to xml_parser_create() failed";
        return false;
    }
    xml_set_element_handler($parser, "MagicParser_xml_analysis_startTag", "MagicParser_xml_analysis_endTag");
    xml_set_character_data_handler($parser, "MagicParser_xml_analysis_cdata");
    $fp = MagicParser_fopen($MagicParser_filename, "r");
    if (!$fp) {
        $MagicParser_error_message = "could not open " . $MagicParser_filename;
        return false;
    }
    $first = true;
    while (!MagicParser_feof($fp)) {
        $xml = MagicParser_fread($fp, 2048);
        if ($first) {
            $xml = ltrim($xml);
            $first = false;
        }
        xml_parse($parser, $xml, false);
    }
    if (MagicParser_feof($fp)) {
        xml_parse($parser, "", true);
    }
    MagicParser_fclose($fp);
    $ignore[] = "ARG";
    $ignore[] = "CATEGORIES";
    $ignore[] = "CATEGORY";
    $ignore[] = "CONTENT";
    $ignore[] = "DC:SUBJECT";
    $ignore[] = "FIELD";
    $ignore[] = "FIELDS";
    $ignore[] = "OPTIONVALUE";
    $ignore[] = "PAYMETHOD";
    $ignore[] = "PRODUCTITEMDETAIL";
    $ignore[] = "PRODUCTREF";
    $ignore[] = "SHIPMETHOD";
    $ignore[] = "TDCATEGORIES";
    $ignore[] = "TDCATEGORY";
    $ignore[] = "MEDIA:THUMBNAIL";
    $repeating_element_count = 0;
    foreach ($MagicParser_xml_analysis_records as $xpath => $data) {
        if ($data["count"] > $repeating_element_count) {
            $ok_to_use = TRUE;
            foreach ($ignore as $v) {
                if (strpos($xpath, $v) !== FALSE) {
                    $ok_to_use = FALSE;
                }
            }
            if ($ok_to_use) {
                $repeating_element_xpath = $xpath;
                $repeating_element_count = $data["count"];
            }
        }
    }
    return $repeating_element_xpath;
}

global $MagicParser_csv_field_separator;
global $MagicParser_csv_text_delimiter;
global $MagicParser_csv_eol_ignorecr;
global $MagicParser_csv_eof;
global $MagicParser_csv_fp;
function MagicParser_csv_read()
{
    global $MagicParser_csv_field_separator;
    global $MagicParser_csv_text_delimiter;
    global $MagicParser_csv_eol_ignorecr;
    global $MagicParser_csv_eof;
    global $MagicParser_csv_fp;
    $done = false;
    $field = "";
    $record = array();
    $position = 0;
    $inquote = false;
    while (!$done) {
        $char = MagicParser_fgetc($MagicParser_csv_fp);
        $usechar = false;
        $commit = false;
        if ($char === false) {
            $MagicParser_csv_eof = true;
            $done = true;
            $commit = true;
        } else switch ($char) {
            case "\r":
                if ($MagicParser_csv_eol_ignorecr) {
                    break;
                }
            case "\n":
                if (($position > 1) && !$inquote) {
                    $commit = true;
                    $done = true;
                }
                break;
            case $MagicParser_csv_field_separator:
                if (!$inquote) {
                    $commit = true;
                } else {
                    $usechar = true;
                }
                break;
            case $MagicParser_csv_text_delimiter:
                if ($MagicParser_csv_text_delimiter != chr(0)) {
                    $inquote = !$inquote;
                }
                break;
            default:
                $usechar = true;
                break;
        }
        if ($usechar) {
            $position++;
            $field .= $char;
        }
        if ($commit && $position) {
            $record[] = $field;
            $field = "";
        }
        if (MagicParser_feof($MagicParser_csv_fp)) {
            $done = true;
        }
    }
    return $record;
}

function MagicParser_csv_parse()
{
    global $MagicParser_format_data;
    global $MagicParser_error_message;
    global $MagicParser_filename;
    global $MagicParser_record_handler;
    global $MagicParser_csv_field_separator;
    global $MagicParser_csv_text_delimiter;
    global $MagicParser_csv_eol_ignorecr;
    global $MagicParser_csv_eof;
    global $MagicParser_csv_fp;
    $MagicParser_csv_field_separator = chr($MagicParser_format_data[1]);
    $MagicParser_csv_text_delimiter = chr($MagicParser_format_data[3]);
    if (isset($MagicParser_format_data[4])) {
        $MagicParser_csv_skip_rows = intval($MagicParser_format_data[4]);
    } else {
        $MagicParser_csv_skip_rows = 0;
    }
    if (isset($MagicParser_format_data[5])) {
        $MagicParser_csv_eol_ignorecr = $MagicParser_format_data[5];
    } else {
        $MagicParser_csv_eol_ignorecr = 0;
    }
    $MagicParser_csv_fp = MagicParser_fopen($MagicParser_filename, "r");
    if (!$MagicParser_csv_fp) {
        $MagicParser_error_message = "could not open " . $MagicParser_filename;
        return false;
    }
    $MagicParser_csv_eof = false;
    $done = false;
    while ($MagicParser_csv_skip_rows--) {
        MagicParser_csv_read();
    }
    if ($MagicParser_format_data[2]) {
        $header = MagicParser_csv_read();
    }
    while (!$MagicParser_csv_eof && !$done) {
        $record = MagicParser_csv_read();
        if (count($record)) {
            $user_record = array();
            $record_pointer = 0;
            if ($MagicParser_format_data[2]) {
                foreach ($header as $v) {
                    $user_record[$v] = $record[$record_pointer];
                    $record_pointer++;
                }
            } else {
                foreach ($record as $v) {
                    $user_record["FIELD" . ($record_pointer + 1)] = $record[$record_pointer];
                    $record_pointer++;
                }
            }
            $done = $MagicParser_record_handler($user_record);
        }
    }
    MagicParser_fclose($MagicParser_csv_fp);
    return true;
}

function MagicParser_csv_analysis()
{
    global $MagicParser_filename;
    $fp = MagicParser_fopen($MagicParser_filename, "r");
    if (!$fp) {
        return "";
    }
    if (!MagicParser_feof($fp)) {
        $data1 = MagicParser_fgets($fp, 4096);
    }
    if (!MagicParser_feof($fp)) {
        $data2 = MagicParser_fgets($fp, 4096);
    }
    MagicParser_fclose($fp);
    if (!$data2) {
        $data2 = $data1;
    }
    $data1 = ltrim($data1);
    $data2 = ltrim($data2);
    if (substr($data1, 0, 4) == "HDR|") {
        return "124|0|0|1";
    }
    unset($field_separator);
    $pipe_count = substr_count($data1, "|");
    $tab_count = substr_count($data1, "\t");
    if ($pipe_count) {
        $field_separator = 124;
    } elseif ($tab_count) {
        $field_separator = 9;
    } else {
        $field_separator = 44;
    }
    unset($header_row);
    if (!isset($header_row)) {
        if (strpos($data1, "http")) {
            $header_row = 0;
        }
        if (strpos($data1, ".")) {
            $header_row = 0;
        }
    }
    if (!isset($header_row)) {
        if (strpos($data1, "product")) {
            $header_row = 1;
        }
        if (strpos($data1, "description")) {
            $header_row = 1;
        }
        if (strpos($data1, "price")) {
            $header_row = 1;
        }
    }
    if (!isset($header_row)) {
        $header_row = 1;
    }
    unset($text_delimiter);
    if (!isset($text_delimiter)) {
        if (strpos($data2, "\"") !== FALSE) {
            $text_delimiter = 34;
        }
    }
    if (!isset($text_delimiter)) {
        if ($data2[0] == "'") {
            $text_delimiter = 39;
        }
    }
    if (!isset($text_delimiter)) {
        $text_delimiter = 0;
    }
    return $field_separator . "|" . $header_row . "|" . $text_delimiter;
}

global $MagicParser_valid_format;
global $MagicParser_record_handler;
global $MagicParser_format_data;
global $MagicParser_error_message;
global $MagicParser_filename;
function MagicParser_parse($filename, $record_handler, $format_string = "")
{
    global $MagicParser_record_count;
    global $MagicParser_record_handler;
    global $MagicParser_format_data;
    global $MagicParser_error_message;
    global $MagicParser_filename;
    $MagicParser_error_message = "";
    if (!$format_string) {
        $format_string = MagicParser_getFormat($filename);
    } else {
        $format_string = strtoupper($format_string);
    }
    if (!$format_string) {
        return false;
    }
    $MagicParser_format_data = explode("|", $format_string);
    $MagicParser_filename = $filename;
    $MagicParser_record_handler = $record_handler;
    $MagicParser_record_count = 0;
    $parse_function = "MagicParser_" . strtolower($MagicParser_format_data[0]) . "_parse";
    if (!function_exists($parse_function)) {
        $MagicParser_error_message = "invalid format string";
        return false;
    }
    $parse_function();
    if ($MagicParser_error_message) {
        return false;
    } else {
        return true;
    }
}

function MagicParser_getFormat($filename)
{
    global $MagicParser_error_message;
    global $MagicParser_filename;
    $MagicParser_filename = $filename;
    $fp = MagicParser_fopen($MagicParser_filename, "r");
    if (!$fp) {
        $MagicParser_error_message = "could not open " . $MagicParser_filename;
        return false;
    }
    $data = "";
    $format_base_type = "";
    do {
        $data .= MagicParser_fread($fp, 64);
        $nlpos = strpos($data, "\n");
        $length = strlen($data);
    } while (($length < 1024) && !$nlpos && !MagicParser_feof($fp));
    MagicParser_fclose($fp);
    if ($nlpos) {
        $data = substr($data, 0, $nlpos);
    }
    $data = ltrim($data);
    if (!$format_base_type) {
        if ($data[0] == "<") {
            $format_base_type = "xml";
        }
    }
    if (!$format_base_type) {
        if (strpos($data, "?xml")) {
            $format_base_type = "xml";
        }
    }
    if (!$format_base_type) {
        $format_base_type = "csv";
    }
    $analysis_function = "MagicParser_" . $format_base_type . "_analysis";
    if (function_exists($analysis_function)) {
        $format_parameters = $analysis_function();
    }
    if (!$format_parameters) {
        $MagicParser_error_message = "autodetect failed";
        return false;
    } else {
        return $format_base_type . "|" . $format_parameters;
    }
}

function MagicParser_getErrorMessage()
{
    global $MagicParser_error_message;
    return $MagicParser_error_message;
}

function MagicParser_createFile($data)
{
    global $MagicParser_error_message;
    $filename = tempnam("", "");
    $fp = @fopen($filename, "w");
    if (!$fp) {
        $MagicParser_error_message = "could not create temporary file";
        return "";
    }
    fwrite($fp, $data);
    fclose($fp);
    return $filename;
}

global $MagicParser_stringPtr;
global $MagicParser_stringOfs;
global $MagicParser_stringDat;
global $MagicParser_stringLen;
function MagicParser_fopen(&$filename, $mode)
{
    global $MagicParser_stringPtr;
    global $MagicParser_stringOfs;
    global $MagicParser_stringDat;
    global $MagicParser_stringLen;
    if (substr($filename, 0, 9) == "string://") {
        if (!isset($MagicParser_stringPtr)) {
            $MagicParser_stringPtr = 0;
            $MagicParser_stringOfs = array();
            $MagicParser_stringDat = array();
            $MagicParser_stringLen = array();
        }
        $MagicParser_stringPtr--;
        $MagicParser_stringOfs[$MagicParser_stringPtr] = 0;
        $MagicParser_stringDat[$MagicParser_stringPtr] = substr($filename, 9);
        $MagicParser_stringLen[$MagicParser_stringPtr] = strlen($MagicParser_stringDat[$MagicParser_stringPtr]);
        return $MagicParser_stringPtr;
    } else {
        return @fopen($filename, $mode);
    }
}

function MagicParser_fread($fp, $length)
{
    global $MagicParser_stringOfs;
    global $MagicParser_stringDat;
    if ($fp < 0) {
        $dat = substr($MagicParser_stringDat[$fp], $MagicParser_stringOfs[$fp], $length);
        $MagicParser_stringOfs[$fp] += $length;
        return $dat;
    } else {
        return @fread($fp, $length);
    }
}

function MagicParser_fgets($fp, $length)
{
    global $MagicParser_stringOfs;
    global $MagicParser_stringDat;
    if ($fp < 0) {
        $dat = "";
        do {
            $chr = substr($MagicParser_stringDat[$fp], $MagicParser_stringOfs[$fp], 1);
            $MagicParser_stringOfs[$fp]++;
            $dat .= $chr;
        } while (($chr <> "\n") && ($length--));
        return $dat;
    } else {
        return @fgets($fp, $length);
    }
}

function MagicParser_fgetc($fp)
{
    global $MagicParser_stringOfs;
    global $MagicParser_stringDat;
    if ($fp < 0) {
        $dat = substr($MagicParser_stringDat[$fp], $MagicParser_stringOfs[$fp], 1);
        $MagicParser_stringOfs[$fp]++;
        return $dat;
    } else {
        return @fgetc($fp);
    }
}

function MagicParser_feof($fp)
{
    global $MagicParser_stringOfs;
    global $MagicParser_stringLen;
    if ($fp < 0) {
        return ($MagicParser_stringOfs[$fp] > $MagicParser_stringLen[$fp]);
    } else {
        return @feof($fp);
    }
}

function MagicParser_fclose($fp)
{
    if ($fp < 0) {
        return;
    } else {
        return @fclose($fp);
    }
} ?>
