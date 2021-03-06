<?php

namespace AbuseIO\Parsers;

use AbuseIO\Models\Incident;

/**
 * Class Arf
 * @package AbuseIO\Parsers
 */
class Arf extends Parser
{
    /**
     * Create a new Arf instance
     *
     * @param \PhpMimeMailParser\Parser $parsedMail phpMimeParser object
     * @param array $arfMail array with ARF detected results
     */
    public function __construct($parsedMail, $arfMail)
    {
        parent::__construct($parsedMail, $arfMail, $this);
    }

    /**
     * Parse attachments
     * @return array    Returns array with failed or success data
     *                  (See parser-common/src/Parser.php) for more info.
     */
    public function parse()
    {

        // TODO - Use feed names with alias style like FBL
        // TODO - Use both DATE fields
        // TODO - Try YAML parser?

        $this->feedName = 'default';

        if ($this->isKnownFeed() && $this->isEnabledFeed() && $this->hasArfMail()) {
            // As this is a generic FBL parser we need to see which was the source and add the name
            // to the report, so its origin is clearly shown.
            $source = $this->parsedMail->getHeader('from');
            foreach (config("{$this->configBase}.parser.aliases") as $from => $alias) {
                if (preg_match($from, $source)) {
                    // If there is an alias, prefer that name instead of the from address
                    $source = $alias;

                    // If there is an more specific feed configuration prefer that config over the default
                    if (!empty(config("{$this->configBase}.feeds.{$source}"))) {
                        $this->feedName = $source;
                    }

                }
            }

            if (preg_match_all('/([\w\-]+): (.*)[ ]*\r?\n/', $this->arfMail['report'] . PHP_EOL, $regs)) {
                $report = array_combine($regs[1], $regs[2]);

                if (empty($report['Received-Date'])) {
                    if (!empty($report['Arrival-Date'])) {
                        $report['Received-Date'] = $report['Arrival-Date'];
                        unset($report['Arrival-Date']);
                    }
                }

                if ($this->hasRequiredFields($report) === true) {
                    // incident has all requirements met, filter and add!
                    if ($report['Feedback-Type'] != 'abuse') {
                        return $this->failed(
                            "Unabled to detect the report type from this notifier"
                        );
                    }

                    $report = $this->applyFilters($report);

                    $report['evidence'] = $this->arfMail['evidence'];

                    $incident = new Incident();
                    $incident->source      = $source;
                    $incident->source_id   = false;
                    $incident->ip          = $report['Source-IP'];
                    $incident->domain      = false;
                    $incident->class       = config("{$this->configBase}.feeds.{$this->feedName}.class");
                    $incident->type        = config("{$this->configBase}.feeds.{$this->feedName}.type");
                    $incident->timestamp   = strtotime($report['Received-Date']);
                    $incident->information = json_encode($report);

                    $this->incidents[] = $incident;

                }
            } else {
                $this->warningCount++;
            }
        }

        return $this->success();
    }
}
