<?php

namespace Concrete\Package\BasicTablePackage\Src\Helpers;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{
    protected $data;
    protected $originalData;
    protected $filename = 'export.csv';

    protected $delimiter = ',';

    protected $enclosure = '"';

    protected $escapeChar = '\\\\';

    public function __construct($data = array(), $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);
        $this->originalData = $data;
        $this->setData($data);
    }

    public function setData(array $data)
    {
        $this->originalData = $data;
        $output = fopen('php://temp', 'r+');

        //first put labels, if not numbers

        $first = true;
        foreach ($data as $row) {
            if ($first) {
                $first = false;
                $oneStringLabel = false;
                $keys = array_keys($row);
                //check if one of them is not a integer
                foreach ($keys as $num => $key) {
                    if (filter_var($key, FILTER_VALIDATE_INT) === false) {
                        $oneStringLabel = true;
                        break;
                    }
                }
                if ($oneStringLabel) {
                    fputcsv($output, $keys, $this->delimiter, $this->enclosure, $this->escapeChar);
                }
            }

            fputcsv($output, $row, $this->delimiter, $this->enclosure, $this->escapeChar);
        }
        rewind($output);
        $this->data = '';
        while ($line = fgets($output)) {
            $this->data .= $line;
        }
        $this->data .= fgets($output);
        return $this->update();
    }

    protected function update()
    {
        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }
        return $this->setContent($this->data);
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this->update();
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return CsvResponse
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        $this->setData($this->originalData);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     * @return CsvResponse
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
        $this->setData($this->originalData);
        return $this;
    }

    /**
     * @return string
     */
    public function getEscapeChar()
    {
        return $this->escapeChar;
    }

    /**
     * @param string $escapeChar
     * @return CsvResponse
     */
    public function setEscapeChar($escapeChar)
    {
        $this->escapeChar = $escapeChar;
        $this->setData($this->originalData);
        return $this;
    }


}