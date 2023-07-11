<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use EurovisionVoting\Init;

class ContestNarrative extends Contest
{
    protected static int $speakSlowness = 24;
    protected int $writeSpeed = 2500000;
    protected static int $waitSpeed = 1;

    public function playResultsNarrative (string $method) : void
    {
        $this->preparePairwiseAndCleanCompute();

        if (Init::$options['fast'] > 0) {
            self::$speakSlowness = 5;
            self::$waitSpeed = 4;
        }

        $method = Condorcet::getMethodClass($method);
        $this->initResult($method);
        $votingMethod = &$this->Calculator[$method];

        //$presentationOrder = array_values(array_flip($method->estimateWeights($this, $this->votingCountries)));
        asort($this->votesbyCountry);
        $presentationOrder = ['SMR', 'ALB', 'CYP']; // array_intersect(array_keys($this->votesbyCountry), $this->votingCountries);
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
        unset ($propabilities, $votes, $totalVotes, $country);

        self::speak("\nHost: ", tools::randSelect(['',"It's that time now."])." Who wants to ".tools::randSelect(['see','hear','know'])." the results to this year's Eurovision Song Contest?");
        self::msleep(rand(3,8) * 100);
        self::write(0, "\n[crowd cheers]\n");
        self::msleep(rand(4,8) * 200);
        self::speak("\nHost:", tools::randSelect(['Just what I thought. B', 'I thought so, but b', "That's what I love to hear. But b", 'B'])."efore we announce the results, we will".tools::randSelect([' ',' first '])."hear from the Executive Supervisor of the Eurovision Song Contest; Martin Osterdahl.\n");
        self::msleep(rand(6,12) * 250);
        self::speak("\nMartin Osterdahl:", "We have checked and verified all of the votes from the ".count($this->votingCountries)." national juries. And with that, the presentation of the results is good to go.\n");
        self::msleep(rand(3,6) * 250);
        self::speak("\nHost:", "Here's how it works. We will connect to".tools::randSelect([' ',' the '])."national juries from the ".count($this->votingCountries)." voting countries, and hear their first choice. We will add the votes from each country's national jury and public one country at a time. After each national jury we visit, we will show the updated results using only votes from the countries we have connected to; both public & jury votes. ");
        self::msleep(rand(2,5) * 150);
        self::speak('', "Now let's connect to our first national jury.");
        self::msleep(rand(3,10) * 250);
        $prevLeaders = [];
        foreach($presentationOrder as $key=>$votingCountry) {
            self::speak("\nHost:", "We are now connecting to ".$this->countryNames[$votingCountry].".\n");
            $time = microtime(true);
            $nationalSimulation = new Contest;
            $nationalSimulation->votingCountries = [$votingCountry];
            $nationalSimulation->copyContestDataForCountry($this, $votingCountry);
            $nationalWinner = $nationalSimulation->getWinner($method)->getName();
            self::msleep(max(1500 - $time - microtime(true), 20));
            self::silence();
            if(rand(0,1) == 1) { self::silence(); }
            $time = microtime(true);
            if (self::isInFrench($votingCountry)) {
                self::speak("\nPresenter from ".$this->countryNames[$votingCountry].": ","Bonsoir Europa. Notre premier choix va ");
                self::silence();  self::write(8, self::FrenchGrammar($nationalWinner)."!\n");
            } else {
                self::speak("\nPresenter from " . $this->countryNames[$votingCountry] . ": ", "Good evening, Europe. Our first choice goes to ");
                if ($votingCountry === 'CYP' /*0R $votingCountry === 'GRC'*/) {
                    $this->sayGreeece();
                    self::msleep(rand(10, 100) * 10);
                } else {
                    self::silence();
                }
                self::write(8, countrydata::replaceInString($nationalWinner) . "!\n");
                self::msleep(rand(5, 20) * 50);
            }
            unset($nationalSimulation);

            $votingMethod->cacheOnePairwise('Jury', $votingCountry);
            $votingMethod->cacheOnePairwise('Public', $votingCountry);
            self::speak("\nHost:", "Now let's see ".tools::randSelect(['the','our'])." results so far.\n\n");
            self::msleep(rand(20,80)*10);
            $currentStanding = $votingMethod->getResult(1);
            self::write(5, $currentStanding->getResultAsString()); self::msleep(250);
            self::write(16, "\n\nMargin from leaders:");
            $leader = $currentStanding->getWinner();
            if (gettype($leader) == 'array') {
                $isTie = true;
                $secondLeader = $leader[1]->getName();
                $leader = $leader[0]->getName();
            } else {
                $isTie = false;
                $leader = $leader->getName();
            }
            $rankings = tools::flatten($currentStanding->getResultAsArray(true));
            foreach ($rankings as $country) {
                if ($country == $leader) $margin = 0;
                else $margin = ($currentStanding->getStats()[$country][$leader]??0) - ($currentStanding->getStats()[$leader][$country]??0);
                self::write(0, "\n".self::evenSpace(40, $this->countryNames[$country]).":\t".round($margin, 3) );
            }
            self::msleep(1000);
            self::write(0,"\n");
            if ($isTie) self::speak("\nHost: ", $leader.' and '.$secondLeader." are currently in the lead. \n");
            elseif ($leader !== end($prevLeaders)) {
                if (in_array($leader, $prevLeaders)) {
                    self::speak("\nHost: ", $leader.tools::randSelect([' is once again in', ' has returned to'])." the lead.\n");
                } else {
                    self::speak("\nHost: ", $leader . " is now in the lead.\n");
                }
            }
            //self::speak("\nHost: ","Now onto the next country.");
            self::msleep(rand(3,8) * 250);
            array_push($prevLeaders, $leader);
        }
        self::speak("\nHost:", "Now finally, we add in the world vote. "); self::msleep(250);
        self::speak('', "Who may our winner be?\n");
        self::silence();
        $finalResult = $votingMethod->getResult(2);
        self::write(5, $finalResult->getResultAsString()); self::msleep(250);
        self::write(16, "\n\nMargin from leaders:");
        $winner = $currentStanding->getWinner()->getName();
        $rankings = tools::flatten($finalResult->getResultAsArray(true));
        foreach ($rankings as $country) {
            $margin = ($currentStanding->getStats()[$country][$leader] ?? 0) - ($currentStanding->getStats()[$leader][$country] ?? 0);
            echo("\n" . self::evenSpace(40, $this->countryNames[$country]) . ":\t" . round($margin, 3));
        }
        self::msleep(rand(4,20)*20);
        self::speak("\n\nHost:", "It's ".$this->countryNames[$winner]."! ");  self::msleep(rand(4,20)*20);
        self::speak('', $this->countryNames[$winner]." has won the Eurovision Song Contest! ");  self::msleep(rand(4,20)*20);
        if (rand(1,5)>2) self::speak('', "What a ".tools::randSelect(['wonderful','spectacular']).' '.tools::randSelect(['evening','night']));
    }

    public static function write (int $slowness, string $message) {
        $strArray = str_split($message);
        foreach ($strArray as $char) {
            $utime = microtime(true);
            echo($char);
            self::msleep($slowness - (microtime(true)-$utime));
            if ($char==='.') self::msleep(2*$slowness+20);
        }
        self::msleep($slowness);
    }

    public static function speak (string $speaker, string $message) {
        $message = countrydata::replaceInString($message);
        self::write(4, $speaker);
        if ($speaker != '') echo ("\t");
        self::write(self::$speakSlowness, $message);
    }

    public static function silence () {
        self::msleep(rand(30, 60)*10);
        for($i=0; $i<3; $i++) {
            self::msleep(250);
            echo('. ');
        }
        self::msleep(250);
        for($i=0; $i<3; $i++) {
            self::msleep(250);
            echo(chr(8).chr(8));
        }
        self::msleep(rand(20, 100)*10);
    }

    protected static function sayGreeece() {
        self::msleep(rand(15,40)*10);
        self::write(4, "\nAudience:");
        self::write(15, "\tGr");
        self::write((self::$speakSlowness)+18, 'eeee');
        echo(chr(8));
        self::write(0, "ce!\n");
        self::msleep(rand(10,20)*10);
    }

    public static function FrenchGrammar(string $string)
    {
        $string = countrydata::replaceInString($string, 'fr');
        if (substr($string, -1) === 'e' || 'l') {
            $string = 'à ' . $string;
        } else {
            $string = 'au '.$string;
        }
        return $string;
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

    public static function msleep (int $milliseconds)
    {
        usleep($milliseconds * 1000 / self::$waitSpeed);
    }

    protected function leaderName ($leader) {
        if (gettype($leader) == 'array') {
            $leader = $leader[array_rand($leader)];
        }
        $name = $leader->getName();
        if (gettype($name) == 'array') $name = $name[0];
        return $name;
    }
}