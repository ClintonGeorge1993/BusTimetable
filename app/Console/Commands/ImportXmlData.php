<?php

namespace App\Console\Commands;

use App\Models\Garages;
use App\Models\JourneyPatterns;
use App\Models\JourneyPatternSection;
use App\Models\JourneyPatternTimingLinks;
use App\Models\JourneyPatternTimingLinkSequence;
use App\Models\Localities;
use App\Models\Operators;
use App\Models\RouteLinksMapping;
use App\Models\Routes;
use App\Models\RouteSections;
use App\Models\RouteLinks;
use App\Models\Services;
use App\Models\StandardServices;
use App\Models\Stoppoints;
use App\Models\VehicleJourneys;
use Illuminate\Console\Command;
use DB;
use function Symfony\Component\String\s;

class ImportXmlData extends Command
{
    protected $signature = 'import:xml-data';
    protected $description = 'Import XML data into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //get all xml file list
        $xml_files = glob(storage_path('route_xml/*.xml'));
        //processing each xml files
        foreach ($xml_files as $xmlFile) {
            // Load and parse the XML data
            $xmlData = simplexml_load_file($xmlFile);
            // Loop through the XML data and insert into the database
            //Saving Localities
            foreach ($xmlData as $xmlElement) {
                $xmlElement = (array)$xmlElement;
                if (!empty($xmlElement['AnnotatedNptgLocalityRef'])) {
                    foreach ($xmlElement['AnnotatedNptgLocalityRef'] as $xmlElement1) {
                        $dup = $this->checkDuplicate(array((string)$xmlElement1->NptgLocalityRef, (string)$xmlElement1->LocalityName), 'Localities');
                        if ($dup === false) {
                            $locality = new Localities();
                            $locality->locality_ref = (string)$xmlElement1->NptgLocalityRef;
                            $locality->locality_name = (string)$xmlElement1->LocalityName;
                            $locality->status = 1;
                            $locality->save();
                        }
                    }
                }
            }
            //Saving StopPoints
            foreach ($xmlData->StopPoints as $xmlElement) {
                $xmlElement = (array)$xmlElement;
                if (!empty($xmlElement['StopPoint'])) {
                    foreach ($xmlElement['StopPoint'] as $xmlElement1) {
                        $locality_id = $this->getId('locality_ref', (string)$xmlElement1->Place->NptgLocalityRef, 'Localities');
                        if ($locality_id > 0) {
                            $dup = $this->checkDuplicate(array((string)$xmlElement1->AtcoCode), 'Stoppoints');
                            if ($dup === false) {
                                $stoppoints = new Stoppoints();
                                $stoppoints->atco_code = (string)$xmlElement1->AtcoCode;
                                $stoppoints->common_name = (string)$xmlElement1->Descriptor->CommonName;
                                $stoppoints->longitude = (string)$xmlElement1->Location->Longitude;
                                $stoppoints->latitude = (string)$xmlElement1->Location->Latitude;
                                $stoppoints->stop_type = (string)$xmlElement1->StopClassification->StopType;
                                $stoppoints->timing_status = (string)$xmlElement1->StopClassification->OffStreet->BusAndCoach->Bay->TimingStatus;
                                $stoppoints->notes = (string)$xmlElement1->Notes;
                                $stoppoints->administrative_area_ref = (string)$xmlElement1->AdministrativeAreaRef;
                                $stoppoints->locality_id = $locality_id;
                                $stoppoints->status = 1;
                                $stoppoints->save();
                            }
                        }
                    }
                }
            }
            //Saving RoutesSections, RouteLinks and RouteLinksMapping
            foreach ($xmlData->RouteSections as $xmlElement) {
                $xmlElement = (array)$xmlElement;
                $result = true;
                if (!empty($xmlElement['RouteSection'])) {
                    foreach ($xmlElement['RouteSection'] as $xmlElement1) {
                        $dup = $this->checkDuplicate(array((string)$xmlElement1['id']), 'RouteSections');
                        if ($dup === false) {
                            $xmlElement_new = (array)$xmlElement1;
                            $routesections = new RouteSections();
                            $routesections->private_code = (string)$xmlElement1['id'];
                            $routesections->status = 1;
                            $result = $routesections->save();
                            if (!empty($xmlElement_new['RouteLink']) && $result) {
                                foreach ($xmlElement_new['RouteLink'] as $xmlElement2) {
                                    $route_link_ref = (string)$xmlElement2['id'];
                                    $route_section_id = $this->getId('private_code', (string)$xmlElement1['id'], 'RouteSections');
                                    $from_stop_point_id = $this->getId('atco_code', (string)$xmlElement2->From->StopPointRef, 'Stoppoints');
                                    $to_stop_point_id = $this->getId('atco_code', (string)$xmlElement2->To->StopPointRef, 'Stoppoints');
                                    $dup = $this->checkDuplicate(array($route_link_ref), 'RouteLinks');
                                    if ($dup === false) {
                                        $routelinks = new RouteLinks();
                                        $routelinks->private_code = $route_link_ref;
                                        $routelinks->route_section_id = $route_section_id;
                                        $routelinks->from_stop_point_id = $from_stop_point_id;
                                        $routelinks->to_stop_point_id = $to_stop_point_id;
                                        $routelinks->distance = (string)$xmlElement2->Distance;
                                        $routelinks->direction = (string)$xmlElement2->Direction;
                                        $routelinks->status = 1;
                                        $result = $routelinks->save();
                                        if ($result) {
                                            $route_link_id = $this->getId('private_code', $route_link_ref, 'RouteLinks');
                                            foreach ($xmlElement2->Track->Mapping->Location as $xmlElement3) {
                                                $routelinksmapping = new RouteLinksMapping();
                                                $routelinksmapping->latitude = (string)$xmlElement3->Latitude;
                                                $routelinksmapping->longitude = (string)$xmlElement3->Longitude;
                                                $routelinksmapping->routelink_id = $route_link_id;
                                                $routelinksmapping->status = 1;
                                                $routelinksmapping->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //Saving Routes
            foreach ($xmlData->Routes as $xmlElement) {
                $xmlElement = (array)$xmlElement;
                if (!empty($xmlElement['Route'])) {
                    foreach ($xmlElement['Route'] as $xmlElement1) {
                        $route_section_id = $this->getId('private_code', (string)$xmlElement1->RouteSectionRef, 'RouteSections');
                        $route_ref = (string)$xmlElement1['id'];
                        if ($route_section_id > 0) {
                            $dup = $this->checkDuplicate(array($route_ref), 'Routes');
                            if ($dup === false) {
                                $routes = new Routes();
                                $routes->private_code = $route_ref;
                                $routes->description = (string)$xmlElement1->Description;
                                $routes->route_section_id = $route_section_id;
                                $routes->status = 1;
                                $routes->save();
                            }
                        }
                    }
                }
            }

            //Saving JourneyPattersSections, JourneyPattersLinks and JourneyPatternTimingLinkSequence
            foreach ($xmlData->JourneyPatternSections as $xmlElement) {
                $xmlElement = (array)$xmlElement;
                $result = true;
                if (!empty($xmlElement['JourneyPatternSection'])) {
                    foreach ($xmlElement['JourneyPatternSection'] as $xmlElement1) {
                        $dup = $this->checkDuplicate(array((string)$xmlElement1['id']), 'JourneyPatternSection');
                        if ($dup === false) {
                            $xmlElement_new = (array)$xmlElement1;
                            $journeysection = new JourneyPatternSection();
                            $journeysection->private_code = (string)$xmlElement1['id'];
                            $journeysection->status = 1;
                            $result = $journeysection->save();
                            if (!empty($xmlElement_new['JourneyPatternTimingLink']) && $result) {
                                foreach ($xmlElement_new['JourneyPatternTimingLink'] as $xmlElement2) {
                                    $journey_link_ref = (string)$xmlElement2['id'];
                                    $journey_section_id = $this->getId('private_code', (string)$xmlElement1['id'], 'JourneyPatternSection');
                                    $route_link_id = $this->getId('private_code', (string)$xmlElement2->RouteLinkRef, 'RouteLinks');
                                    $dup = $this->checkDuplicate(array($journey_link_ref), 'JourneyPatternTimingLinks');
                                    if ($dup === false) {
                                        $journeylinks = new JourneyPatternTimingLinks();
                                        $journeylinks->private_code = $journey_link_ref;
                                        $journeylinks->routelink_ref = $xmlElement2->RouteLinkRef;
                                        $journeylinks->runtime = $xmlElement2->RunTime;
                                        $journeylinks->journeypatternsection_id = $journey_section_id;
                                        $journeylinks->routelink_id = $route_link_id;
                                        $journeylinks->status = 1;
                                        $result = $journeylinks->save();
                                        if ($result) {
                                            $from_sequence_num = (string)$xmlElement2->From['SequenceNumber'];
                                            $to_sequence_num = (string)$xmlElement2->To['SequenceNumber'];
                                            $from_stop_point_id = $this->getId('atco_code', (string)$xmlElement2->From->StopPointRef, 'StopPoints');
                                            $to_stop_point_id = $this->getId('atco_code', (string)$xmlElement2->To->StopPointRef, 'StopPoints');
                                            $journey_link_id = $this->getId('private_code', $journey_link_ref, 'JourneyPatternTimingLinks');
                                            $journeylinkseq = new JourneyPatternTimingLinkSequence();
                                            $journeylinkseq->from_sequence_number = $from_sequence_num;
                                            $journeylinkseq->to_sequence_number = $to_sequence_num;
                                            $journeylinkseq->from_activity = (string)$xmlElement2->From->Activity;
                                            $journeylinkseq->to_activity = (string)$xmlElement2->To->Activity;
                                            $journeylinkseq->from_dynamic_destination = (string)$xmlElement2->From->DynamicDestinationDisplay;
                                            $journeylinkseq->to_dynamic_destination = (string)$xmlElement2->To->DynamicDestinationDisplay;
                                            $journeylinkseq->from_stop_point_id = $from_stop_point_id;
                                            $journeylinkseq->to_stop_point_id = $to_stop_point_id;
                                            $journeylinkseq->from_timing_status = (string)$xmlElement2->From->TimingStatus;
                                            $journeylinkseq->to_timing_status = (string)$xmlElement2->To->TimingStatus;
                                            $journeylinkseq->journeypatterntiminglink_id = $journey_link_id;
                                            $journeylinkseq->status = 1;
                                            $journeylinkseq->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //Saving Operators and Garages
            foreach ($xmlData->Operators->Operator as $xmlElement) {
                $dup = $this->checkDuplicate(array((string)$xmlElement['id']), 'Operators');
                if ($dup === false) {
                    $operators = new Operators();
                    $address = (array)$xmlElement->OperatorAddresses->CorrespondenceAddress->Line;
                    $operators->private_code = (string)$xmlElement['id'];
                    $operators->national_operator_code = (string)$xmlElement->NationalOperatorCode;
                    $operators->operator_code = (string)$xmlElement->OperatorCode;
                    $operators->operator_short_name = (string)$xmlElement->OperatorShortName;
                    $operators->operator_name_on_licence = (string)$xmlElement->OperatorNameOnLicence;
                    $operators->trading_name = (string)$xmlElement->TradingName;
                    $operators->licence_number = (string)$xmlElement->LicenceNumber;
                    $operators->licence_classification = (string)$xmlElement->LicenceClassification;
                    $operators->address1 = (string)$address[0];
                    $operators->address2 = (string)$address[1];
                    $operators->address3 = (string)$address[2];
                    $operators->address4 = (string)$address[3];
                    $operators->status = 1;
                    $result = $operators->save();
                    if ($result) {
                        foreach ($xmlElement->Garages->Garage as $xmlElement1) {
                            $dup = $this->checkDuplicate(array((string)$xmlElement1->GarageCode), 'Garages');
                            if ($dup === false) {
                                $operator_id = $this->getId('private_code', (string)$xmlElement['id'], 'Operators');
                                if ($operator_id > 0) {
                                    $garages = new Garages();
                                    $garages->garage_code = (string)$xmlElement1->GarageCode;
                                    $garages->garage_name = (string)$xmlElement1->GarageName;
                                    $garages->longitude = (string)$xmlElement1->Location->Longitude;
                                    $garages->latitude = (string)$xmlElement1->Location->Latitude;
                                    $garages->operator_id = $operator_id;
                                    $garages->status = 1;
                                    $garages->save();
                                }
                            }
                        }
                    }
                }
            }
            //Saving Services, StandardServices and JourneyPatters
            foreach ($xmlData->Services->Service as $xmlElement) {
                $start_date = date('Y-m-d', strtotime((string)$xmlElement->OperatingPeriod->StartDate));
                $end_date = date('Y-m-d', strtotime((string)$xmlElement->OperatingPeriod->EndDate));
                $operator_id = $this->getId('private_code', (string)$xmlElement->RegisteredOperatorRef, 'Operators');
                $result = true;
                $dup = $this->checkDuplicate(array((string)$xmlElement->ServiceCode), 'Services');
                if ($dup === false) {
                    $services = new Services();
                    $services->service_code = (string)$xmlElement->ServiceCode;
                    $services->private_code = (string)$xmlElement->PrivateCode;
                    $services->line_id = (string)$xmlElement->Lines->Line['id'];
                    $services->line_name = (string)$xmlElement->Lines->Line->LineName;
                    $services->start_date = $start_date;
                    $services->end_date = $end_date;
                    $services->operator_id = $operator_id;
                    $services->mode = (string)$xmlElement->Mode;
                    $services->status = 1;
                    $result = $services->save();

                    if ($result) {
                        $service_id = $this->getId('service_code', (string)$xmlElement->ServiceCode, 'Services');
                        $standardservices = new StandardServices();
                        $standardservices->origin = (string)$xmlElement->StandardService->Origin;
                        $standardservices->destination = (string)$xmlElement->StandardService->Destination;
                        $standardservices->service_id = $service_id;
                        $standardservices->status = 1;
                        $result = $standardservices->save();
                        if ($result) {
                            foreach ($xmlElement->StandardService->JourneyPattern as $xmlElement1) {
                                $dup = $this->checkDuplicate(array((string)$xmlElement1['id']), 'JourneyPatterns');
                                if ($dup === false) {
                                    $standardservice_id = $this->getId('service_id', $service_id, 'StandardServices');
                                    $route_id = $this->getId('private_code', (string)$xmlElement1->RouteRef, 'Routes');
                                    $jps_id = $this->getId('private_code', (string)$xmlElement1->JourneyPatternSectionRefs, 'JourneyPatternSection');
                                    if ($route_id > 0 && $jps_id > 0 && $standardservice_id > 0) {
                                        $journeypatterns = new JourneyPatterns();
                                        $journeypatterns->private_code = (string)$xmlElement1['id'];
                                        $journeypatterns->destination_display = (string)$xmlElement1->DestinationDisplay;
                                        $journeypatterns->direction = (string)$xmlElement1->Location->Direction;
                                        $journeypatterns->standard_service_id = $standardservice_id;
                                        $journeypatterns->route_id = $route_id;
                                        $journeypatterns->journey_pattern_section_id = $jps_id;
                                        $journeypatterns->status = 1;
                                        $journeypatterns->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //Save VehicleJourneys
            foreach ($xmlData->VehicleJourneys->VehicleJourney as $xmlElement) {
                ;
                $garage_id = $this->getId('garage_code', (string)$xmlElement->GarageRef, 'Garages');
                $service_id = $this->getId('service_code', (string)$xmlElement->ServiceRef, 'Services');
                $journeypattern_id = $this->getId('private_code', (string)$xmlElement->JourneyPatternRef, 'JourneyPatterns');
                $dup = $this->checkDuplicate(array((string)$xmlElement->PrivateCode), 'VehicleJourneys');
                if ($dup === false && $journeypattern_id > 0 && $service_id > 0 && $garage_id > 0) {
                    $vehiclejourney = new VehicleJourneys();
                    $vehiclejourney->private_code = (string)$xmlElement->PrivateCode;
                    $vehiclejourney->description = (string)$xmlElement->Operational->Block->Description;
                    $vehiclejourney->block_number = (string)$xmlElement->Operational->Block->BlockNumber;
                    $vehiclejourney->ticket_machine_service_code = (string)$xmlElement->Operational->TicketMachine->TicketMachineServiceCode;
                    $vehiclejourney->journey_code = (string)$xmlElement->Operational->TicketMachine->JourneyCode;
                    $vehiclejourney->layover_point_duration = (string)$xmlElement->LayoverPoint->Duration;
                    $vehiclejourney->layover_point_name = (string)$xmlElement->LayoverPoint->Name;
                    $vehiclejourney->layover_latitude = '';
                    $vehiclejourney->layover_longitude = '';
                    if (!empty($xmlElement->LayoverPoint->Location)) {
                        $vehiclejourney->layover_latitude = (string)$xmlElement->LayoverPoint->Location->Latitude;
                        $vehiclejourney->layover_longitude = (string)$xmlElement->LayoverPoint->Location->Longitude;
                    }
                    $vehiclejourney->vehicle_journey_code = (string)$xmlElement->VehicleJourneyCode;
                    $vehiclejourney->garage_id = $garage_id;
                    $vehiclejourney->service_id = $service_id;
                    $vehiclejourney->line_ref = (string)$xmlElement->LineRef;
                    $vehiclejourney->journey_pattern_id = $journeypattern_id;
                    $vehiclejourney->departure_time = (string)$xmlElement->DepartureTime;
                    $vehiclejourney->status = 1;
                    $vehiclejourney->save();
                }
            }
        }
        $this->info('XML data imported successfully.');
    }

    //Function to check duplication
    public function checkDuplicate($columns, $table){
        if($table == 'Localities') {
            $details = DB::table($table)
                ->select('id')
                ->where('locality_ref', '=', $columns[0])
                ->where('locality_name', '=', $columns[1])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'Stoppoints'){
            $details = DB::table($table)
                ->select('id')
                ->where('atco_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'RouteSections'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'RouteLinks'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'Routes'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'JourneyPatternSection'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'JourneyPatternTimingLinks'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'Operators'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'Garages'){
            $details = DB::table($table)
                ->select('id')
                ->where('garage_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'Services'){
            $details = DB::table($table)
                ->select('id')
                ->where('service_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'JourneyPatterns'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }else if($table == 'VehicleJourneys'){
            $details = DB::table($table)
                ->select('id')
                ->where('private_code', '=', $columns[0])
                ->where('status', '=', 1)
                ->get();
            $details = json_decode(json_encode($details));
            if(count($details) > 0)
                return true;
            else
                return false;
        }
    }

    //Function to get IDs
    public function getId($column, $reference, $table){
        $details = DB::table($table)
            ->select('id')
            ->where($column, '=', $reference)
            ->where('status', '=', 1)
            ->get();
        $details = json_decode(json_encode($details));
        if(count($details) > 0)
            return $details[0]->id;
        else
            return 0;
    }
}
