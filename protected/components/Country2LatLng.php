<?php
/**
 * Класс, выводящий код страны по IP
 */
class Country2LatLng extends CApplicationComponent {
	private $data = array();
    private $fp;

    /**
     * Initializes the ip to country lookup tables and preloads the index.
     */
    public function __construct()
    {
        $datafile = ROOTPATH . '/include/country2latlng.csv';
        $this->fp = fopen($datafile, 'r');
        $this->preload();
    }

    /**
     * Destroys the ip 2 country instance.
     */
	public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    /**
     * Preloads the entire data file into memory for faster lookups of many IP
     * addresses
     */
    public function preload()
    {
        if ($this->fp == null) {
            return;
        }

        while (($row = fgetcsv($this->fp, 256, ",")) !== FALSE) {
            $this->data[$row[0]] = array(
                'lat' => $row[1],
                'lng' => $row[2],
                'country' => $row[3],
                'zoom' => $row[4],
            );
        }

        fclose($this->fp);
        $this->fp = null;
    }

    /**
     * Lookup an IP address and return the two-letter ISO country.
     */
    public function lookup($country)
    {
        $country = strtoupper($country);
        return $this->data[$country];
    }
}
