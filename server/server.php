<?php

// Configuration

$start=new DateTime("now");

date_default_timezone_set("Asia/Jakarta");

$running_connection=0;
$server=[
    "IDN"=>[
        "score"=>0,
        "time_utc"=>date("[D] H::m::s",time()),
        "status"=>true
    ],
    "UK"=>[
        "score"=>0,
        "time_utc"=>date("[D] H::m::s",time()-21600),
        "status"=>true
    ],
    "US"=>[
        "score"=>0,
        "time_utc"=>date("[D] H::m::s",time()-39600),
        "status"=>true
        ]
    ];

$default_offline=2;

generated_auto_key($server);

// Logic

while(true)
{

    // Random number from 1 to total $server + 10 ( simulate server offline )

    $offline_server=rand(1,count($server)+10);

    // increase score value from $server for offline server

    $data_server=counter($server,$offline_server);

    // Print data from $server

    log_data($data_server,$server);

    // Delay from 1 second to execute next code
    
    sleep(1);

    // increment our connection
    
    $running_connection++;

    // Checking if found server for $default_offline for first times

    if(get_offline_score($default_offline,$server))
    {

        // Calculate time for timing

        $total_time=$start->diff(new DateTime('now'));

        // Report

        echo "\n\t\t Report \n\n";

        foreach($server as $k=>$v)
        {
            echo $k."\t | has offline for ".$v['score']." times\n";
        }

        // Timing Report

        echo "\nMachine was run for ".($running_connection*3)." connections ( ".$total_time->format("%H hour %m Minutes %s Second")." )\n";

        // Report location offline server

        $n=array_filter($server,function($arr)use($default_offline){
            return $arr['score']==$default_offline;
        });

        echo "=================================\n";

        echo key($n)." was offline for $default_offline times, Please do maintenance immediately.\n";
       
        break;
    }
    
}

// Global Function

function get_offline_score($default_offline,$data) 
{
    foreach($data as $k=>$v)
    {
        if($v['score']==$default_offline)
        {
            return true;
        }
    }
}

function counter(&$server,$offline_server)
{

    if(array_key_exists($offline_server,SERVER))
    {
        $server[SERVER[$offline_server]]["score"]++;
        $server[SERVER[$offline_server]]["status"]=false;
    }

    return $server;

}

function generated_auto_key($server)
{
    // Generated CONSTANT for checking $server key

   define("SERVER",array_keys($server));
}

function log_data($data,&$server)
{
    foreach($data as $k=>$v)
    {
        $show_problem=(!$v['status']) ? "!![OFF] OFFLINE " :$v['time_utc'];
        echo " | ".$k." -- ".$show_problem;

        $server[$k]['status']=true;
    }

    echo PHP_EOL;
}