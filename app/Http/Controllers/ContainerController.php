<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('index');
    }

    public function indexData()
    {
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -a -o status,name,uuid,hostname,ip_configured,type,description -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $response = json_decode($result, true);
        return view('index-data', ['data' => $response]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('show', ['id' => $id]);
    }
    public function showData($id)
    {
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -i '.$id.' -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $responseData = json_decode($result, true);


        $streamStat = ssh2_exec($connection, 'prlctl exec '.$id.' /root/psutil');
        stream_set_blocking($streamStat, true);
        $streamStat_out = ssh2_fetch_stream($streamStat, SSH2_STREAM_STDIO);
        $resultStat = stream_get_contents($streamStat_out);
        $responseStat = json_decode($resultStat, true);



        return view('show-data', ['data' => $responseData[0], 'stat' => $responseStat]);
         // dd($responseStat);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function state($id, $action)
    {
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl reset-uptime '.$id.' & prlctl set '.$id.' & prlctl '.$action.' '.$id.' &');
        return redirect('/ct/'.$id);
    }
}
