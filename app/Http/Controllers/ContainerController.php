<?php

namespace App\Http\Controllers;
use App\Container; 
use App\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

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

        $user = auth()->user()->id;
        $db = User::find($user)->containers->toArray();
        $collection = collect($db);
        $pluck = $collection->pluck('name');


		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}

        $result = $ssh->exec('prlctl list -a -o status,name,hostname,ip_configured,description -j');

        $response = json_decode($result, true);
        $col = collect($response);
        $filtered = $col->whereIn('name', $pluck);
        $data = $filtered->values()->all();

        return view('index-data', ['data' => $data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->first();
        if ($db->user_id != auth()->user()->id) {
           echo "404";
           exit();
        }
        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }
        else {

        return view('show', ['id' => $id]);
        }
    }
    public function showData($id)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->first();
        if ($db->user_id != auth()->user()->id || $db->suspended === 1) {
           echo "404";
           exit();
        }
        else {

		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}

        $result = $ssh->exec('prlctl list -i '.$id.' -j');    
        $responseData = json_decode($result, true);
        $resultStat = $ssh->exec('prlctl exec '.$id.' /usr/local/bin/monit'); 
        $responseStat = json_decode($resultStat, true);

        return view('show-data', ['data' => $responseData[0], 'stat' => $responseStat]);

        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->firstOrFail();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }
        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }
        else {
		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}      

        $result = $ssh->exec('prlctl list -i '.$id.' -j');
        $responseData = json_decode($result, true);

        return view('edit', ['data' => $responseData[0]]);        	
        }

        
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
        $user = auth()->user()->id;
        $db = Container::where('name', $id)->firstOrFail();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }
        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }

		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}    

        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }
        if ($request['password']) {
            $passwduser = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 12);
            $result = $ssh->exec('prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname'].' --userpasswd root:'.$passwduser);
            \Session::flash('pwgen', $passwduser);            
        } 
        else {
            $result = $ssh->exec('prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname']);
        }

        return redirect('/ct/'.$request['name']);
    }


    public function state($id, $action)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->first();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }     

        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }
		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}    
  
        $result = $ssh->exec('prlctl '.$action.' '.$id.' > /dev/null &');
        $result = $ssh->exec('prlctl reset-uptime '.$id);
        if ($action == "start") {
            $result = $ssh->exec('prlctl set '.$id.' --autostart on');
        }
        elseif ($action == "stop") {
            $result = $ssh->exec('prlctl set '.$id.' --autostart off');
        }
        else {
            $result = $ssh->exec('prlctl set '.$id);
        }

        return redirect('/ct/'.$id);
    }


    public function rebuild($id)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->first();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }
 
        elseif ($db->suspended === 1) {
        	\Session::flash('suspended'); 
        	return redirect('/ct');
        }
		$key = new RSA();
		$key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
		$ssh = new SSH2(config('ovz.ssh_ip'));
		if (!$ssh->login('root', $key)) {
		    exit('Login Failed');
		}                   
        $result = $ssh->exec('prlctl list -i '.$id.' -j');
        $responseData = json_decode($result, true);

        return view('rebuild', ['data' => $responseData[0]]);
        
    }
}
