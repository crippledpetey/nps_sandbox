<?php
function remoteReportExists($report){
     //check if file exists
     $curl = curl_init( 'http://www.needplumbingsupplies.com/tpsreports/' . $report . '.txt');

     //don't fetch the actual page, you only want to check the connection is ok
     curl_setopt($curl, CURLOPT_NOBODY, true);

     //do request
     $result = curl_exec($curl);

     $ret = false;

     //if request did not fail
     if ($result !== false) {

        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        //if is active
        if ($statusCode == 200) {
            $ret = true;   
        }
     }

     //close curl session
     curl_close($curl);

     //return status
     return $ret;
}

//file locations
$tables = array(
     'affiliatelog',
     'affiliates',
     'billing',
     'blogs',
     'blogsreviews',
     'brands',
     'categories',
     'checkoutsteps',
     'concatcategories',
     'configuration',
     'configuration1',
     'containerbox',
     'content',
     'contentcategories',
     'coupons',
     'currencyvalues',
     'customergroups',
     'customerprices',
     'customers',
     'downloads',
     'dtproperties',
     'gifts',
     'groups',
     'hackers',
     'ImportProcess',
     'InventoryScratch',
     'languages',
     'menuitem',
     'mycompany',
     'news',
     'newsletter',
     'newsreviews',
     'oitems',
     'oitems2',
     'orders',
     'orders2',
     'ordertracking',
     'ordertracking2',
     'pinnumbers',
     'prodcategories',
     'prodfeatures',
     'ProdfeaturesImagefix',
     'products',
     'productsnew',
     'ProductsQuotes',
     'productvariants',
     'projects',
     'quantitydiscounts',
     'registrant',
     'registryitems',
     'reviews',
     'savedcarts',
     'savedquery',
     'searchresults',
     'shipmethods',
     'shopcountries',
     'shoppingcom',
     'shoprma',
     'shopstates',
     'sitesessions',
     'stocknotification',
     'suppliers',
     'tblaccess',
     'tblaudithist',
     'tbllog',
     'tblmenuheader',
     'tbluser',
     'templates',
     'translateblogs',
     'translatecategories',
     'translatecontent',
     'translatecontentcategories',
     'translatenews',
     'translateprodfeatures',
     'translateproducts',
     'xmlfeed'
     );

//if url command to get report is set
$page_content = '';

//add select bo to the page output
if( isset( $_GET['get_report'] ) ){

     //set report var
     $report = $_GET['get_report'];

     //check if report exists
     $exists = remoteReportExists( $report );

     $file_location = 'http://www.needplumbingsupplies.com/tpsreports/';

     //get all?
     if( $report == 'all' ){
          foreach ($tables as $key => $report) {
               $exists = remoteReportExists( $report );
               if ($exists) {
                    $file = file_get_contents( $file_location . $report . '.txt');
                    file_put_contents("/home/sandbox/old_reports/" . $report . '.txt', $file);
                    $page_content .= "Created " . strtoupper( $report ) . " file \n<a download='" . $report . ".txt' href='http://www.needplumbingsupplies.com/tpsreports/" . $report . ".txt'>Click Here To Download File</a>\n";
               } else {
                    $page_content .= "No " . strtoupper( $report ) . " file present\n";
               }

               ob_flush();
               flush();
          }
     }elseif( in_array( $report, $tables ) ){
          $exists = remoteReportExists( $report );
          if ($exists) {
               $file = file_get_contents($file_location . $report . '.txt');
               file_put_contents("/home/sandbox/old_reports/" . $report . '.txt', $file);
               $page_content .= "Created " . strtoupper( $report ) . " file \n<a download='" . $report . ".txt' href='http://www.needplumbingsupplies.com/tpsreports/" . $report . ".txt'>Click Here To Download File</a>\n";
          } else {
               $page_content .= "No " . strtoupper( $report ) . " file present\n";
          }

          ob_flush();
          flush();
     } 

     echo '<div><a href="http://sandbox.needplumbingsupplies.com/get_reports.php">Back</a></div>';

} else {
     //if url command to get report is set
     $page_content = '<h1>Data Export Service</h1><form method="get"><select name="get_report"><option calue="all">ALL REPORTS</option>';
     foreach( $tables as $report ){
          $page_content .='<option value="' . $report . '">' . strtoupper( $report ) . '</option>';
     }
     $page_content .= '</select><input type="submit" value="Get Report(s)"></form>';
}

echo $page_content;
?>


