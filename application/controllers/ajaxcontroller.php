<?php

/***
 *      /$$$$$$     /$$$$$  /$$$$$$  /$$   /$$
 *     /$$__  $$   |__  $$ /$$__  $$| $$  / $$
 *    | $$  \ $$      | $$| $$  \ $$|  $$/ $$/
 *    | $$$$$$$$      | $$| $$$$$$$$ \  $$$$/
 *    | $$__  $$ /$$  | $$| $$__  $$  >$$  $$
 *    | $$  | $$| $$  | $$| $$  | $$ /$$/\  $$
 *    | $$  | $$|  $$$$$$/| $$  | $$| $$  \ $$
 *    |__/  |__/ \______/ |__/  |__/|__/  |__/
 *
 *  This is the ajax view, which is a very important part of the website. All of the methods in this class will be
 *  pulled via jQuery's $.ajax() method and can be dynamically loaded and changed without refreshing the page.
 */

class AjaxController extends Controller{

    protected $deviceObject;
    protected $studentObject;
    protected $analyticsObject;
    protected $logObject;
    protected $locationObject;
    protected $notificationsObject;
    protected $schoolObject;


    //If someone tries to directly access the ajax view it will return "invalid request"
    public function index(){
        $this->set('response', "Invalid Request");
    }

    public function devicesearch(){
        $asset = $_POST['asset']; //Submitted from views/device/index.php #assetSearch form
        $this->deviceObject = new Device();
        $results = $this->deviceObject->getDeviceInformationByAsset($asset);
        $damageHistory = $this->deviceObject->getDeviceDamageByAsset($asset);
        $this->set('deviceinfo', $results);
        $this->set('damageHistory', $damageHistory);
    }

    //NOTE: This method returns the SAME information as devicesearch, it just has to first get the asset by serial but even the view is a copy and paste of devicesearch
    public function devicesearchbyserial(){
        $serial = $_POST['sn']; //Submitted from views/device/index.php #serialSearch form
        $this->deviceObject = new Device();
        $results = $this->deviceObject->getAssetBySerial($serial);
        $deviceInfo = $this->deviceObject->getDeviceInformationByAsset($results['CopyBarcode']);
        $damageHistory = $this->deviceObject->getDeviceDamageByAsset($results['CopyBarcode']);
        $this->set('deviceinfo', $deviceInfo);
        $this->set('damageHistory', $damageHistory);
    }

    public function rtisearchcase(){
        $case_number = $_POST['case']; //Submitted from views/rti/index.php #caseSearch form
        $this->logObject = new Log();
        $results = $this->logObject->getRTILogByCase($case_number);

        $this->set('case', $results);
    }
    public function rtisearchinvoice(){
        $invoice_number = $_POST['invoice']; //Submitted from views/rti/index.php #invoiceSearch form
        $this->logObject = new Log();
        $results = $this->logObject->getRTILogByInvoice($invoice_number);

        $this->set('invoice', $results);
    }
    /*****************************************
     * TypeAhead AutoPopulate Search Methods *
     *****************************************/
    /*
     * The methods usernamesearch, assetsearch and location search poll the database with what is typed in the input forms to return a list of results that match
     * the TypeAhead JS library then uses the data from the database to autopopulate suggestions.
     */

    public function usernamesearch($query){
        $this->studentObject = new Student();
        $json = $this->studentObject->searchName($query);
        $this->set('json', $json);
    }
    //This is for the new record form, it works with the Typeahead JS lbrary to try to autocomplete by searching the query in the database and dynamically updating the autocomplete as the user types
    public function assetsearch($query){
        $this->deviceObject = new Device();
        $json = $this->deviceObject->searchAsset($query);
        $this->set('json', $json);
    }

    //
    public function locationsearch($query){
        $this->locationObject = new Location();
        $json = $this->locationObject->searchLocation($query);
        $this->set('json', $json);
    }


    public function roomsearch(){
        $school = $_POST['code'];
        $this->locationObject = new Location();
        $this->analyticsObject = new Analytics();
        $this->schoolObject = new School();
        $SchoolName = $this->analyticsObject->getDestinyRefFromCode($school);
        $json = $this->locationObject->getRoomsInSchool($SchoolName);
        $this->set('json', $json);
    }
    /*****End TypeAhead Methods******/

    //This is for pulling the room number and building of a device and setting those values on the new_record form
    public function assetLocationSearch(){
        $asset = $_POST['asset'];
        //Get the SiteName (School) and Room the Asset is stored in
        $this->deviceObject = new Device();
        $results = $this->deviceObject->getDeviceInformationByAsset($asset);

        //Change Destiny's SiteName into the SchoolCode in the Damage Database
        $this->analyticsObject = new Analytics();
        $schoolCode = $this->analyticsObject->getCodeFromDestinyRef($results['SiteName']);

        if($schoolCode == 'WC' OR $schoolCode == 'SS')
            $room = 'HS'; //Chromebooks are not set in a room in the High School
        else
            $room = $results['Room'];


        //Return the School and Room the asset is in
        $this->set('school', $schoolCode);
        $this->set('room', $room);
        $this->set('studentID', $results['DistrictID']);

    }


    //Grabs information about a student by name
    public function usersearch(){
        $name= $_POST['name'];
        $this->studentObject = new Student();
        $demographics = $this->studentObject->getStudentDemographicInfo($name);
        $destinyinfo = $this->studentObject->getStudentAssets($demographics['Alpha_Key']);
        $studentHistory = $this->studentObject->getDamageHistory($demographics['Alpha_Key']);
        $this->set('name', $name);
        $this->set('userinfo', $demographics);
        $this->set('userassets',$destinyinfo);
        $this->set('studentHistory', $studentHistory);
    }

    //Grabs information about the default repair type for a school
    public function defaultRepairType(){
        $school = $_GET['code'];
        $this->schoolObject = new School();
        $schoolInfo = $this->schoolObject->getSchoolInfo($school);
        $this->set('schoolInfo', $schoolInfo);
    }

    //After the end user selects a school from the drop down on the school view, this generates a dashboard with information for just that school.
    public function schoolpick(){
        //This is the school CODE
        $school=$_POST['school'];
        //Instantiate the analytics object
        $this->analyticsObject = new Analytics();
        //Grab name of school (for RTI Reporting)
        $schoolName = $this->analyticsObject->getSchoolNameFromCode($school);
        //Grab name of school (for Destiny Reporting)
        $DestinySchoolName = $this->analyticsObject->getDestinyRefFromCode($school);

        //Total Amount of Damage Records specific for a school
        $totalRecords = $this->analyticsObject->getTotalRecordsBySchool($school);
        //Total Amount of Damage in the entire distrcit
        $districtTotal = $this->analyticsObject->getTotalRecords();
        //Returns an array of damage by room in a building
        $damageByRoom = $this->analyticsObject->getDamageByRoomAtSchool($school);
        //Returns an array of damage by damage type in a building
        $damageTypes = $this->analyticsObject->getCountDamageBySchool($school);
        //Returns the dollar amount of damage from RTI invoices on damage for a school
        $totalDamage = $this->analyticsObject->getDamageTotalsBySchool($school);

        /* Note: For MS / IA, the query returns a double array like so:
         *  ----------------------------------
         *  | NAME |   MS_Total  |  IA_Total |
         *  | ---- |   --------  |  -------- |
         *  | NULL |   346       |  0        |
         *  | RPIA |   0         |  389      |
         *  ----------------------------------
         *
         * The MS total will only be in the first row, MS_TOTAL column, and the IA Total will ONLY b in the second row IA_TOTAL Column
         * So [0]['MS_Total'] will grab the correct for MS and
         * [1]['IA_Total'] will grab the correct for IA
         */
        //else we need to use a custom method to branch the devices by intermediate academy
        if($school =='CI' || $school =='RI' || $school =='SI'){
            $chromebookAssts = $this->analyticsObject->getDamageableAssetsForMiddleSchool($DestinySchoolName, 'Chromebook');
            $chromebookAssets = $chromebookAssts[1]['IA_Total']; //See explanation above
            $iPadAssts = $this->analyticsObject->getDamageableAssetsForMiddleSchool($DestinySchoolName, 'iPad');
            $iPadAssets = $iPadAssts[1]['IA_Total']; //See explanation above
            $totalAssets = $chromebookAssets + $iPadAssets;
        }
        elseif($school =='CM' || $school =='RM' || $school =='SM'){
            $chromebookAssts = $this->analyticsObject->getDamageableAssetsForMiddleSchool($DestinySchoolName, 'Chromebook');
            $chromebookAssets = $chromebookAssts[0]['MS_Total']; //See explanation above
            $iPadAssts = $this->analyticsObject->getDamageableAssetsForMiddleSchool($DestinySchoolName, 'iPad');
            $iPadAssets = $iPadAssts[0]['MS_Total']; //See explanation above
            $totalAssets = $chromebookAssets + $iPadAssets;
        }
        //If the school code is NOT a middle school or intermediate academy, grab this data
        else {
            //Grab just chromebook assets from a particular school
            $chromebookAssts = $this->analyticsObject->getDamageableAssetsBySchool($DestinySchoolName, 'Chromebook');
            //Pass the total here instead of doing the legwork in the view
            $chromebookAssets = $chromebookAssts['Total'];
            //Grab just iPad Assets from a Particular School
            $iPadAssts = $this->analyticsObject->getDamageableAssetsBySchool($DestinySchoolName, 'iPad');
            $iPadAssets = $iPadAssts['Total'];
            //Combine iPad and Chromebooks for the total assets in a school
            $totalAssets = $chromebookAssets + $iPadAssets;
        }



        //Send these values to the view for displaying in charts and stats
        $this->set('schoolCode', $school);
        $this->set('totalRec', $totalRecords);
        $this->set('districtTotal', $districtTotal);
        $this->set('damageTypes', $damageTypes);
        $this->set('roomDamage', $damageByRoom);
        $this->set('totalDamage', $totalDamage);

        $this->set('chromebookAssets', $chromebookAssets);
        $this->set('iPadAssets', $iPadAssets);
        $this->set('totalAssets', $totalAssets);

    }

    /*
     * These methods are used exclusively on the record view
     */

    //View that displays a form on the record view to edit a specific issue
    public function editIssue($issueID){
        $this->logObject = new Log();
        $issue = $this->logObject->getIssue($issueID);
        $issuesList = $this->logObject->getIssuesList();

        $this->set("issue", $issue);
        $this->set('issuesList', $issuesList);
    }
    //Takes the information from the editIssue view and submits it to the database
    public function submitEditIssue(){
        $recordID = $_POST['recordID'];
        $this->logObject = new Log();
        $data = array("Issue"=>$_POST['issue'], "IssueCode"=>$_POST['issueCode'], "IssueRecordID"=>$_POST['issueID']);
        $result = $this->logObject->editIssue($data);

        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        $url = BASE_URL . "log/record/" . $recordID;

        header("Location: " . $url);
    }

    //View that displays a form on the record view to add a new issue
    public function addIssue($recordID){
        $this->logObject = new Log();
        $issuesList = $this->logObject->getIssuesList();
        $this->set('issuesList', $issuesList);
        $this->set('recordID', $recordID);
    }
    //Takes the information from the addIssue view and submits it to the database
    public function submitNewIssue(){
        $this->logObject = new Log();
        $data = array("Issue"=>$_POST['issue'], "IssueCode"=>$_POST['issueCode'], "RecordID"=>$_POST['recordID']);

        $result = $this->logObject->addIssue($data);
        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        header("Location: " .  BASE_URL . "log/record/" .$_POST['recordID'] );
    }

    //Displays a form for editing the Damage Information for a Particular Record
    public function editDamage($recordID){
        $this->logObject = new Log();

        //Get info about the record passed to help autofill the edit form with pre-existing data
        $info = $this->logObject->getDeviceDamageInfo($recordID);

        $this->set('info',$info);
    }

    public function submitEditDamage(){
        $this->logObject = new Log();

        $recordID = $_POST['recordID'];

        //Grab the post data from the form
        $data = array($_POST['logdate'], $_POST['asset'], $_POST['school'], $_POST['room'], $_POST['ticket'], $_POST['name'], $_POST['description'], $_POST['notes'], $_POST['recordID']);

        $result = $this->logObject->editDamageInfo($data);
        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        header("Location: " . BASE_URL . 'log/record/' . $recordID);
    }

    public function editLog($RTIrecordID){
        $this->logObject = new Log();
        $info = $this->logObject->getRTIlogInfo($RTIrecordID);
        $this->set('info', $info);
    }
    public function submitEditLog(){
        $this->logObject = new Log();
        $data = array($_POST["datePackaged"], $_POST["trackingNum"],$_POST["dateReturned"], $_POST["studentCharged"],$_POST["ID"]);
        $result = $this->logObject->editRTILogInfo($data);
        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        header("Location: " . BASE_URL . 'log/record/' . $_POST['damageRecord']);
    }


    //View that displays a form on the internal record view to edit a specific part
    public function editpart($recordID,$partID){
        $this->logObject = new Log();
        $part = $this->logObject->getPart($partID);
        $partsList = $this->logObject->getPartsList();

        $this->set('recordID', $recordID);
        $this->set("partInfo", $part);
        $this->set('partsList', $partsList);
    }
    //Takes the information from the editPart view and submits it to the database
    public function submitEditPart(){
        $recordID = $_POST['recordID'];
        $this->logObject = new Log();
        $part = $_POST['part']; //Structured like: "LCD|50"
        $results = explode('|', $part); //Turns into an array of {["LCD", 50]}
        $data = array("PartID"=>$_POST['partID'], "Part"=>$results[0], "Cost"=>$results[1]);
        $result = $this->logObject->editPart($data);

        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        $url = BASE_URL . "log/record/" . $recordID;

        header("Location: " . $url);
    }

    public function editInternalLog($recordID){
        $this->logObject = new Log();
        $info = $this->logObject->getInternalLogInfo($recordID);
        $this->set('info', $info);
    }

    public function submitEditInternalLog(){
        $this->logObject = new Log();

        $data = array('InternalRecordID'=>$_POST['internalRecordID'],'Date_Repaired'=>$_POST['date_repaired'],'Cost'=>$_POST['cost'],'Student_Charged'=>$_POST['studentCharged'], 'Notes'=>$_POST['notes']);

        $result = $this->logObject->editInternalLog($data);
        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];
        header("Location: " .  BASE_URL . "log/record/" .$_POST['recordID'] );
    }

    //View that displays a form on the record view to add a new part
    public function addpart($recordID, $InternalRecordID){
        $this->logObject = new Log();
        $partsList = $this->logObject->getPartsList();
        $this->set('partsList', $partsList);
        $this->set('internalRecordID', $InternalRecordID);
        $this->set('recordID', $recordID);
    }
    //Takes the information from the addPart view and submits it to the database
    public function submitNewPart(){
        $this->logObject = new Log();
        $part = $_POST['part']; //Structured like: "LCD|50"
        $results = explode('|', $part); //Turns into an array of {["LCD", 50]}
        $data = array("Part"=>$results[0], "Cost"=>$results[1], "InternalRecordID"=>$_POST['internalRecordID']);

        $result = $this->logObject->addPart($data);
        $_SESSION['edit_message'] = $result['Message'];
        $_SESSION['edit_msg_type'] = $result['Type'];

        header("Location: " .  BASE_URL . "log/record/" .$_POST['recordID'] );
    }

    //Grabs fine information for a student
    public function finesearch(){
        $name= $_POST['name'];
        $this->studentObject = new Student();

        $demographics = $this->studentObject->getStudentDemographicInfo($name);
        $AlphaKey = $demographics['Alpha_Key'];
        $fines = $this->studentObject->getFineInfo($AlphaKey);

        $this->set('userinfo', $demographics);
        $this->set('fines',$fines);
    }

    //This is for the notifications drop down menu
    public function notifications(){
        $this->notificationsObject = new Notifications();
        $this->userObject = new User();

        $user = $this->userObject->getApplicationUserInfo();
        $userID = $user['UserID'];

        //Get the counts of the notifications
        $newNotificationsCount = $this->notificationsObject->getNotificationCount('new', $userID);
        $totalNotificationsCount = $this->notificationsObject->getNotificationCount('total', $userID);


        //Get the actual notifications in a list
        $newNotifications = $this->notificationsObject->getNotifications('new', $userID);
        $totalNotifications = $this->notificationsObject->getNotifications('total', $userID);

        $this->set("newCount", $newNotificationsCount);
        $this->set("totalCount", $totalNotificationsCount);
        $this->set('new', $newNotifications);
        $this->set('total', $totalNotifications);


    }

    public function dismissNotification($notificationID){
        $this->notificationsObject = new Notifications();
        $result = $this->notificationsObject->dismissNotification($notificationID);
        $this->set('result',$result);
    }


}
