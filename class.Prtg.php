<?php
/**
 * Created by PhpStorm.
 * User: javaci
 * Date: 2020-08-30
 * Time: 12:22
 */
define("IPORDOMAIN","");
define("USERNAME", "");
define("PASSHASH", "");
define("PRTG_ENABLED",true);

class Prtg
{
    public $userName;
    public $objId;

    public function __construct($userName = "")
    {
        $this->userName = $userName;
    }

    public function getUserObjId()
    {
        if (!PRTG_ENABLED){
            return false;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://".IPORDOMAIN."/api/table.xml?content=sensors&columns=objid,group,device,sensor,status,message,lastvalue,priority,favorite&username=" . USERNAME . "&passhash=" . PASSHASH,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $xml = new SimpleXMLElement($response);
        $arr = (array)$xml;
        $baseArray = array();
        foreach ($arr["item"] as $item) {
            $item = (array)$item;
            if (strpos($item["sensor"], $this->userName) !== false) {
                array_push($baseArray, $item);
            }
        }
        if (isset($baseArray[0])) {

            foreach ($baseArray as $item) {
                if ($item["status_raw"] == "3" or $item["status_raw"] == 3){
                    $this->objId = $item["objid"];
                    return true;
                }
            }
            $this->objId = $baseArray[count($baseArray) - 1 ]["objid"];
            return true;
        }
        return false;
    }

    public function getUserGraphOnSvg($graphId = 0)
    {
        if (!PRTG_ENABLED){
            return false;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://".IPORDOMAIN."/chart.svg?type=graph&graphid=".$graphId."&width=1500&height=500&tooltexts=1&refreshable=true&columns=datetime%2Cvalue_%2Ccoverage&id=" . $this->objId . "&graphstyling=baseFontSize%3D%2710%27%20showLegend%3D%271%27&tooltexts=1" . $this->objId . "&username=" . USERNAME . "&passhash=" . PASSHASH,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getAnaInternet($interface, $graphID,$type)
    {
        if (!PRTG_ENABLED){
            return false;
        }
        $id = "";
        if ($interface == "sfp"){
            $id = 2027;
        }
        if ($interface == "nida"){
            $id=  2030;
        }
        $curl = curl_init();
        $urlStr =  "http://".IPORDOMAIN."//chart.svg?type=graph&graphid=".$graphID."&width=1500&height=500&tooltexts=1&refreshable=true&columns=datetime%2Cvalue_%2Ccoverage&_=1591881406779&id=" .$id."&hide=&tooltexts=1&username=" . USERNAME . "&passhash=" . PASSHASH;
        if ($type == "url"){
            return "<br><a href=\"".$urlStr."\"  target='_blank'> <button class='btn btn-success'> Büyüt </button></a>";
        }
//        echo $urlStr."\n";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlStr,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getUserInternet($graphID, $userName )
    {
        if (!PRTG_ENABLED){
            return false;
        }
        $this->userName  = $userName;
        $this->getUserObjId();
       return $this->getUserGraphOnSvg($graphID);

    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getObjId()
    {
        return $this->objId;
    }

    /**
     * @param mixed $objId
     */
    public function setObjId($objId): void
    {
        $this->objId = $objId;
    }




}