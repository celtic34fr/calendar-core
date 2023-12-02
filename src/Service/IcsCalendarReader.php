<?php

namespace Celtic34fr\CalendarCore\Service;

use Celtic34fr\CalendarCore\Traits\R34ICS_Calendar;

class IcsCalendarReader
{
    /**
     * ICAL file as array
     * @var array|null
     */
    private $ical = null;

    /**
     * Tracks the number of alarms in the current iCal feed
     *  (Room34CreativeServices)
     * @var integer
     */
    public $alarmCount = 0;

    /**
     * Tracks the number of events in the current iCal feed
     *  (Room34CreativeServices)
     * @var integer
     */
    public $eventCount = 0;

    /**
     * Tracks the free/busy count in the current iCal feed
     *  (Room34CreativeServices)
     * @var integer
     */
    public $freeBusyCount = 0;

    /**
     * Tracks the number of todos in the current iCal feed
     *  (Room34CreativeServices)
     * @var integer
     */
    public $todoCount = 0;

    /**
     * Tracks the number of journals in the current iCal feed
     * @var integer
     */
    public $journalCount = 0;


    use R34ICS_Calendar;

    /**
     * Load ICSfiçle and transform it in array
     * @param string $data
     * @return void
     */
    public function &load(string $data): array
    {
        $this->ical = false;
		$regex_opt = 'mib';

		// Fix issue with hard line breaks inside DESCRIPTION fields (not included in the documentation
        // because problem needs further research)
        $data = $this->r34ics_line_break_fix($data);

		// Lines in the string
		$lines = mb_split( '[\r\n]+', $data );
		// Delete empty ones
		$last = count( $lines );
		for($i = 0; $i < $last; $i ++) {
			if (trim( $lines[$i] ) == '') {
				unset( $lines[$i] );
            }
		}
		$lines = array_values( $lines );

        // First and last items
		$first = 0;
		$last = count( $lines ) - 1;
		if (! ( mb_ereg_match( '^BEGIN:VCALENDAR', $lines[$first], $regex_opt ) 
                and mb_ereg_match( '^END:VCALENDAR', $lines[$last], $regex_opt ) )) {
			$first = null;
			$last = null;
			foreach ( $lines as $i => &$line ) {
				if (mb_ereg_match( '^BEGIN:VCALENDAR', $line, $regex_opt )) {
					$first = $i;
                }
				if (mb_ereg_match( '^END:VCALENDAR', $line, $regex_opt )) {
					$last = $i;
					break;
				}
			}
		}

		// Procesing
		if (!is_null( $first ) and !is_null( $last )) {
            $this->ical = [];
			$lignes = array_slice( $lines, $first + 1, ( $last - $first - 1 ), true );
            while (!empty($lignes)) {
                $ligne = array_shift($lignes);

                /** suppression des caractères non affichages (Room34CreativeServices) */
                $ligne = $this->removeUnprintableChars($ligne);
                if (empty($ligne)) {
                    continue;
                }
                if (!$this->disableCharacterReplacement) {
                    $ligne = str_replace(array(
                        '&nbsp;',
                        "\t",
                        "\xc2\xa0", // Non-breaking space
                    ), ' ', $line);

                    $ligne = $this->cleanCharacters($ligne);
                }
                /** suppression des caractères non affichages (Room34CreativeServices) */

                /** éclatement de la ligne en tokens / valeurs (Room34CreativeServices) */
                $add     = $this->keyValueFromString($ligne);
                $keyword = $add[0];
                $values  = $add[1]; // May be an array containing multiple values
                if (!is_array($values)) {
                    if (!empty($values)) {
                        $values     = array($values); // Make an array as not one already
                        $blankArray = array(); // Empty placeholder array
                        $values[]   = $blankArray;
                    } else {
                        $values = array(); // Use blank array to ignore this line
                    }
                } elseif (empty($values[0])) {
                    $values = array(); // Use blank array to ignore this line
                }
                // Reverse so that our array of properties is processed first
                $values = array_reverse($values);
                /** éclatement de la ligne en tokens / valeurs (Room34CreativeServices) */

                if (!$this->startWith($ligne, 'BEGIN')) {
                    $item = $this->extractItem($ligne);
                    $this->ical = array_merge($this->ical, $item);
                } else {
                    $blocName = substr($ligne, strpos($ligne, ':') + 1);

                    /** appel sous-routine pour travail du bloc d'information
                     * @param array $lignes tableau des lignes sans la ligne de début de bloc
                     * @param string $blocName nom de bloc à traiter jusqu'à END:blocName
                     * @return array lignes restante après extraction du bloc
                     * @return array bloc à insérer dans ical à la clé blocName
                     */
                    list($lignes, $bloc) = $this->extractBloc($lignes, $blocName);
                    if ($blocName == "VEVENT") {
                        $key = 0;
                        $current = [];
                        if (array_key_exists($blocName, $this->ical)) {
                            $key = array_key_last($this->ical[$blocName]) + 1;
                            $current = end($this->ical[$blocName]);
                        }
                        $current = array_merge($current, $bloc);
                        if (!array_key_exists($blocName, $this->ical)) $this->ical[$blocName] = [];
                        $this->ical[$blocName][$key] = $current;
                    } else {
                        if (!array_key_exists($blocName, $this->ical)) $this->ical[$blocName] = [];
                        $this->ical[$blocName] = $bloc;
                    }
                }
            }
        }
		return $this->ical;
    }

    /**
     * Determine if string begin with substring or not
     * @param string $reference
     * @param string $tofound
     * @return boolean
     */
    private function startWith(string $reference, string $tofound): bool
    {
        return (strpos($reference, $tofound) === 0);
    }

    /**
     * Trandform a line of ICS file into an array
     * @param string $ligne
     * @return array
     */
    private function extractItem(string $ligne): array
    {
        $item = explode(":", $ligne);
        $extract = [];

        switch(true) {
            // RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
            case $this->startWith($ligne, 'RRULE') :
                $local = explode(";", $item[1]);
                $tmp = [];
                foreach ($local as $elt) {
                    $elt = explode("=", $elt);
                    $tmp[$elt[0]] = $elt[1];
                }
                $extract['RRULE'] = $tmp;
                break;
            // DTSTART;TZID=Asia/Kolkata:2014-04-19T19:30:00 DTEND:2014-04-19T21:30:00
            case $this->startWith($ligne, "DTSTART") :
            case $this->startWith($ligne, "DTEND") :
                $start = substr($ligne, 0, strpos($ligne, ":"));
                if (sizeof($item) > 2) {
                    $i = 2;
                    while ($i < sizeof($item)) {
                        $item[1] .= ":".$item[$i];
                        $i++;
                    }
                }
                $elt = explode(";", $item[0]);
                $start = $elt[0];

                if ($start === $item[0]) {
                    $extract[$item[0]] = $item[1];
                } else {
                    $elt = explode("=", $elt[1]);
                    $extract[$start] = [$elt[0] => $elt[1], "VALUE" => $item[1]];
                }
                break;
            case $this->startWith($ligne, "DESCRIPTION"):
                $key = array_shift($item);
                $value = implode(":", $item);
                $extract[$key] = $value;
                break;
            // ORGANIZER;CN=Jean-Charles:mailto:example@gmail.com
            case $this->startWith($ligne, "ORGANIZER"):
                $params = explode(";", $item['0']);
                $tmp = array_shift($params);
                if ($params) {
                    $elt = explode("=", $params[0]);
                    $extract[$elt[0]] = $elt[1];
                }

                if (sizeof($item) == 3) {
                    $extract[$item[1]] = $item[2];
                }
                $extract = ['ORGANIZER' => $extract];
                break;
            // ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;
            // CN=user@example.se:mailto:user@example.se
            case $this->startWith($ligne, "ATTENDEE"):
                $params = explode(";", $item['0']);
                $tmp = array_shift($params);
                foreach ($params as $elt) {
                    $elt = explode("=", $elt);
                    $extract[strtoupper($elt[0])] = array_key_exists(1, $elt) ? $elt[1] : $item[1];
                }
                if (sizeof($item) == 3) $extract[$item[1]] = $item[2];
                $extract = ['ATTENDEE' => $extract];
                break;
            // ATTACH;FMTTYPE=audio/basic:http://example.com/pub/audio-files/ssbanner.aud
            case $this->startWith($ligne, 'ATTACH'):
                if (in_array('HTTP', $item) || in_array('Http', $item) || in_array('http', $item)) {
                    $key = array_search('HTTP', $item);
                    $key = !$key ? array_search('Http', $item) : $key;
                    $key = !$key ? array_search('http', $item) : $key;
                    $item[$key] = $item[$key] .':'. $item[$key + 1];
                    unset($item[$key + 1]);
                }
                $local = explode(";", $item[0]);
                $tmp = array_shift($local);
                foreach ($local as $elt) {
                    $elt = explode("=", $elt);
                    $extract[strtoupper($elt[0])] = $elt[1];
                }
                $extract['URL'] = $item[1];
                $extract = ['ATTACH' => $extract];
                break;
            case $this->startWith($ligne, "CATEGORIES"):
                $item[1] = explode(",", $item[1]);
                $extract[$item[0]] = $item[1];
                break;
            case $this->startWith($ligne, "GEO"):
                $params = explode(';', $item[1]);
                $extract['GEO'] = [
                    'LATITUDE' => $params[0],
                    'LONGITUDE' => $params[1],
                ];
                break;
            default:
                $extract[$item[0]] = $item[1];
        }
        return $extract;
    }

    /**
     * Aggregate all line in bloc determine by BEGIN:nameOfBlock ... END/nameOfBlock
     * @param array $lignes
     * @param string $blocName
     * @return void
     */
    private function extractBloc(array $lignes, string $blocName)
    {
        $bloc = [];
        $blocEame = "";
        while ($blocName !== $blocEame) {
            $ligne = array_shift($lignes);
            if ($this->startWith($ligne, 'BEGIN')) {
                $blocTmp = substr($ligne, strpos($ligne, ':') + 1);
                list($lignes, $tmp) = $this->extractBloc($lignes, $blocTmp);
                $bloc[$blocTmp] = $tmp;
            } elseif ($this->startWith($ligne, 'END')) {
                $blocEame = substr($ligne, strpos($ligne, ':') + 1);
            } else {
                $item = $this->extractItem($ligne);
                if (array_key_first($item) == "FREEBUSY") {
                    if (!array_key_exists('FREEBUSY', $bloc)) $bloc['FREEBUSY'] = [];
                    $freebusys = array_merge($bloc["FREEBUSY"], [$item['FREEBUSY']]);
                    $bloc['FREEBUSY'] = $freebusys;
                } elseif (array_key_first($item) == 'ATTENDEE') {
                    if (!array_key_exists('ATTENDEE', $bloc)) $bloc['ATTENDEE'] = [];
                    $attendees = array_merge($bloc['ATTENDEE'], [$item['ATTENDEE']]);
                    $bloc['ATTENDEE'] = $attendees;
                } else {
                    $bloc = array_merge($bloc, $item);
                }
            }
        }
        return [$lignes, $bloc];
    }
}