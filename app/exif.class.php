<?php

class exif {
    private $mExif = null;

    public function exif($path) {
        $this->mExif = @exif_read_data($path, null, true);
    }

    private function gps2num($coordPart) {
        $parts = explode('/', $coordPart);
        if(count($parts) <= 0)
            return 0;
        if(count($parts) == 1)
            return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }

    private function convert_gps($exifCoord, $ref) {
        $degrees= count($exifCoord) > 0 ? $this->gps2num($exifCoord[0]) : 0;
        $minutes= count($exifCoord) > 1 ? $this->gps2num($exifCoord[1]) : 0;
        $seconds= count($exifCoord) > 2 ? $this->gps2num($exifCoord[2]) : 0;
        $minutes += 60 * ($degrees- floor($degrees));
        $degrees = floor($degrees);
        $seconds += 60 * ($minutes- floor($minutes));
        $minutes = floor($minutes);
        if ($seconds >= 60) {
            $minutes += floor($seconds/60.0);
            $seconds -= 60*floor($seconds/60.0);
        }
        if ($minutes >= 60) {
            $degrees += floor($minutes/60.0);
            $minutes -= 60*floor($minutes/60.0);
        }
        $lng_lat = $degrees + $minutes / 60 + $seconds / 60 / 60;
        if (strtoupper($ref == 'W') || strtoupper($ref) == 'S') {
            $lng_lat = $lng_lat * -1;
        }
        return $lng_lat;
    }

    public function location() {
        if (!isset($this->mExif["GPS"]["GPSLatitude"])) {
            return array();
        }

        $latitude = $this->convert_gps($this->mExif["GPS"]["GPSLatitude"], $this->mExif["GPS"]["GPSLatitudeRef"]);
        $longitude = $this->convert_gps($this->mExif["GPS"]["GPSLongitude"], $this->mExif["GPS"]["GPSLongitudeRef"]);
        return array("latitude" => $latitude, "longitude" => $longitude);
    }
};


