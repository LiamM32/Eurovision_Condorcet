<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class ContestNarrative extends Contest
{
    protected static int $speakSpeed = 0;//24;
    protected int $writeSpeed = 2500000;
    protected int $waitSpeed = 2;

    public function playResultsNarrative (string $method) : void
    {
        $this->preparePairwiseAndCleanCompute();

        $method = Condorcet::getMethodClass($method);
        $this->initResult($method);
        $votingMethod = &$this->Calculator[$method];

        //$presentationOrder = array_values(array_flip($method->estimateWeights($this, $this->votingCountries)));
        asort($this->votesbyCountry);
        $presentationOrder = ['HRV']; // array_intersect(array_keys($this->votesbyCountry), $this->votingCountries);
        $probabilities = []; //tools::array_reciprocal($this->votesbyCountry, rand(1,6));
        $totalVotes = $this->countVotes();
        foreach ($this->votingCountries as $country) {
            $votes = $this->countVotes($country);
            if ($votes > 0) $probabilities[$country] = $totalVotes / $votes;
        }
        while(tools::count($presentationOrder) < count($probabilities)) {
            $country = tools::getRandomWeightedElement($probabilities);
            array_push($presentationOrder, $country);
            unset ($probabilities[$country]);
        }
        unset ($propabilities, $votes, $totalVotes);

        self::speak("\nHost: ", tools::randSelect(['',"It's that time now."])." Who wants to ".tools::randSelect(['see','hear','know'])." the results to this year's Eurovision Song Contest?");
        usleep(rand(3,8) * 100000);
        self::write(0, "\n[crowd cheers]\n");
        usleep(rand(4,8) * 200000);
        self::speak("\nHost:", tools::randSelect(['Just what I thought. B', 'I thought so, but b', "That's what I love to hear. But b", 'B'])."efore we announce the results, we will".tools::randSelect([' ',' first '])."hear from the Executive Supervisor of the Eurovision Song Contest; Martin Osterdahl.\n");
        usleep(rand(6,12) * 250000);
        self::speak("\nMartin Osterdahl:", "We have checked and verified all of the votes from the ".count($this->votingCountries)." national juries. And with that, the presentation of the results is good to go.\n");
        usleep(rand(3,6) * 250000);
        self::speak("\nHost:", "Here's how it works. We will connect to".tools::randSelect([' ',' the '])."national juries from the ".count($this->votingCountries)." voting countries, and hear their first choice. We will add the votes from each country's national jury and public one country at a time. After each national jury we visit, we will show the updated results using only votes from the countries we have connected to; both public & jury votes. ");
        usleep(rand(1,3) * 250000);
        self::speak('', "Now let's connect to our first national jury.");
        usleep(rand(3,10) * 250000);
        $prevLeaders = [];
        foreach($presentationOrder as $key=>$country) {
            self::speak("\nHost:", "We are now connecting to ".$this->countryNames[$country]."\n");
            $time = microtime(true);
            $nationalSimulation = new Contest;
            $nationalSimulation->votingCountries = [$country];
            $nationalSimulation->copyContestDataForCountry($this, $country);
            $nationalWinner = $nationalSimulation->getWinner($method)->getName();
            usleep(max(1500000 - $time - microtime(true), 20000));
            self::silence();
            if(rand(0,1) == 1) { self::silence(); }
            $time = microtime(true);
            if (self::isInFrench($country)) {
                self::speak("\nPresenter from ".$this->countryNames[$country].": ","Bonsoir Europa. Notre premier choix va ");
                self::silence();  self::write(8, self::FrenchGrammar($nationalWinner)."!\n\n");
            } else {
                self::speak("\nPresenter from " . $this->countryNames[$country] . ": ", "Good evening, Europe. Our first choice goes to ");
                if ($country === 'CYP' /*0R $country === 'GRC'*/) {
                    $this->sayGreeece();
                    usleep(rand(10, 100) * 10000);
                } else {
                    self::silence();
                }
                self::write(8, $nationalWinner . "!\n\n");
            }
            unset($nationalSimulation);

            $votingMethod->cacheOnePairwise('Jury', $country);
            $votingMethod->cacheOnePairwise('Public', $country);
            self::speak("\nHost:", "Now let's see ".tools::randSelect(['the','our'])." results so far.\n\n");
            usleep(rand(20,80)*10000);
            $currentStanding = $votingMethod->getResult(true);
            self::write(5, $currentStanding->getResultAsString()); usleep(250000);
            self::write(16, "\n\nMargin from leaders:");
            $leader = $currentStanding->getWinner()->getName();
            $rankings = tools::flatten($currentStanding->getResultAsArray(true));
            foreach ($rankings as $country) {
                $margin = ($currentStanding->getStats()[$country][$leader]??0) - ($currentStanding->getStats()[$leader][$country]??0);
                self::write(0, "\n".self::evenSpace(40, $this->countryNames[$country]).":\t".round($margin, 3) );
            }
            sleep(1);
            self::write(0,"\n");
            if ($leader !== end($prevLeaders)) {
                if (in_array($leader, $prevLeaders)) {
                    self::speak("\nHost: ", $leader.tools::randSelect([' is once again in', ' has returned to'])." the lead.\n");
                } else {
                    self::speak("\nHost: ", $leader . " is now in the lead.\n");
                }
            }
            //self::speak("\nHost: ","Now onto the next country.");
            usleep(rand(3,8) * 250000);
            array_push($prevLeaders, $leader);
        }
        self::speak("\nHost:", "Now finally, we add in the world vote. "); usleep(250000);
        self::speak('', "Who may our winner be?\n");
        self::silence();
        $finalResult = $votingMethod->getResult(2);
        self::write(5, $finalResult->getResultAsString()); usleep(250000);
        self::write(16, "\n\nMargin from leaders:");
        $winner = $currentStanding->getWinner()->getName();
        $rankings = tools::flatten($finalResult->getResultAsArray(true));
        foreach ($rankings as $country) {
            $margin = ($rankings->getStats()[$country][$leader] ?? 0) - ($currentStanding->getStats()[$leader][$country] ?? 0);
            self::write(0, "\n" . self::evenSpace(40, $this->countryNames[$country]) . ":\t" . round($margin, 3));
        }
        usleep(rand(4,20)*20000);
        self::speak("\n\nHost:", "It's ".$winner."! ");  usleep(rand(4,20)*20000);
        self::speak('', $winner." has won the Eurovision Song Contest! ");  usleep(rand(4,20)*20000);
        if (rand(1,5)>2) self::speak('', "What a ".tools::randSelect(['wonderful','spectacular']).' '.tools::randSelect(['evening','night']));
    }

    public static function write (int $slowness, string $message) {
        $strArray = str_split($message);
        foreach ($strArray as $char) {
            //$utime = microtime(true);
            echo($char);
            usleep($slowness * 1000 /*- (microtime(true)-$utime)*/);
        }
    }

    public static function speak (string $speaker, string $message) {
        //$message = countrydata::replaceInString($message);
        self::write(4, $speaker);
        if ($speaker != '') echo ("\t");
        self::write(self::$speakSpeed, $message);
    }

    public static function silence () {
        usleep(rand(30, 60)*10000);
        for($i=0; $i<3; $i++) {
            usleep(250000);
            echo('. ');
        }
        usleep(250000);
        for($i=0; $i<3; $i++) {
            usleep(250000);
            echo(chr(8).chr(8));
        }
        usleep(rand(20, 100)*10000);
    }

    protected static function sayGreeece() {
        usleep(rand(15,40)*10000);
        self::write(4, "\nAudience:");
        self::write(15, "\tGr");
        self::write((self::$speakSpeed)+10, 'eeeeece ');
        echo(chr(8).chr(8).chr(8).chr(8).chr(8));
        self::write(0, "ce!\n");
        usleep(rand(10,20)*10000);
    }

    public static function FrenchGrammar(string $string)
    {
        $string = countrydata::replaceInString($string, 'fr');
        if (substr($string, -1) === 'e' || 'l') {
            $string = 'à ' . $string;
        } else {
            $string = 'au '.$string;
        }
        return string;
    }

    public static function isInFrench(string $country)
    {
        switch ($country) {
            case 'FRA': $chance = 5.5;
            case 'BEL': $chance = 2.5;
            case 'LUX': $chance = 4;
            case 'ITA': $chance = 2;
            case 'ESP': $chance = 1;
            case 'CHE': $chance = 1.75;
            case 'ALB': $chance = 0.75;
            case 'GRC': $chance = 0.5;
            default:    $chance = 0;
        }
        if ($chance*4 >= rand(0,24)) return true;
        else return false;
    }

    public static function evenSpace (int $L, string $string): string
    {
        $L -= max(0, strlen($string));
        for ($i=0; $i<$L; $i++) {
            $string .= ' ';
        }
        return $string;
    }
}