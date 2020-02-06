<?php

namespace App\Http\Controllers;
use App\Container; 
use App\User; 
use App\Ip4address; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -a -o status,name,hostname,ip_configured,description -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $response = json_decode($result, true);


        $col = collect($response);
        $filtered = $col->whereIn('name', $pluck);
        $data = $filtered->values()->all();

        return view('index-data', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $getAddress = Ip4address::where('container_id', null);

        if ($getAddress->count()) {
            $getAddress = Ip4address::where('container_id', null)->first();
            $getUser = User::where('email', $request['email']);

            if ($getUser->count()) {
                $id = Container::create()->id;
                $userInfo = User::where('email', $request['email'])->first();
                Container::where('id', $id)->update(['name' => 'ct'.$id, 'user_id' => $userInfo->id, 'locked' => false]);
                $res = ['name' => "ct".$id, 'ip' => $getAddress->address, 'new'=> false];
                Ip4address::where('id', $getAddress->id)->update(['container_id' => $id]);
                shell_exec('/opt/php72/bin/php /var/www/sanes/data/www/ovz.vcloud.net.ru/artisan container:create --name=ct'.$id.
                    ' --ostemplate=ubuntu-18.04 --cpus=2 --ipadd='.$getAddress->address.'> /dev/null &');
                return response($res, 200, ['Content-Type' => 'application/json;charset=UTF-8'])->header('Access-Control-Allow-Origin', '*');
            } 

                else {
                $user = new User();
                $user->password = Hash::make('Pass123@@@');
                $user->email = $request['email'];
                $user->name = $request['email'];
                $user->save();

                $userInfo = User::where('email', $request['email'])->first();
                $id = Container::create()->id;
                Container::where('id', $id)->update(['name' => 'ct'.$id, 'user_id' => $userInfo->id, 'locked' => false]);
                $res = ['name' => "ct".$id, 'ip' => $getAddress->address, 'new'=> true];
                Ip4address::where('id', $getAddress->id)->update(['container_id' => $id]);

                shell_exec('/opt/php72/bin/php /var/www/sanes/data/www/ovz.vcloud.net.ru/artisan container:create --name=ct'.$id.
                    ' --ostemplate=ubuntu-18.04 --cpus=2 --ipadd='.$getAddress->address.' > /dev/null &');
                return response($res, 200)->header('Access-Control-Allow-Origin', '*');
                }
            } 

            else {
                return response('', 404)->header('Access-Control-Allow-Origin', '*');
            }

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

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->firstOrFail();
        if ($db->user_id != auth()->user()->id) {
           echo "404";
           exit();
        }
        else {
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -i '.$id.' -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $responseData = json_decode($result, true);


        $streamStat = ssh2_exec($connection, 'prlctl exec '.$id.' /usr/local/bin/psutil');
        stream_set_blocking($streamStat, true);
        $streamStat_out = ssh2_fetch_stream($streamStat, SSH2_STREAM_STDIO);
        $resultStat = stream_get_contents($streamStat_out);
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
                
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -i '.$id.' -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $responseData = json_decode($result, true);

        return view('edit', ['data' => $responseData[0]]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // dd($request);
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        if ($request['password']) {
            $passwduser = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 12);
            $stream = ssh2_exec($connection, 'prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname']);
            // $stream = ssh2_exec($connection, 'prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname'].' --userpasswd root:'.$passwduser);
            \Session::flash('pwgen', $passwduser);            
        } 
        else {
            $stream = ssh2_exec($connection, 'prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname']);
        }
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);

        return redirect('/ct/'.$request['name']);
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

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->firstOrFail();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }        
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl reset-uptime '.$id.' & prlctl '.$action.' '.$id.' &');
        if ($action == "start") {
            $stream = ssh2_exec($connection, 'prlctl set '.$id.' --autostart on');
        }
        elseif ($action == "stop") {
            $stream = ssh2_exec($connection, 'prlctl set '.$id.' --autostart off');
        }
        else {
            $stream = ssh2_exec($connection, 'prlctl set '.$id);
        }

        return redirect('/ct/'.$id);
    }


    public function rebuild($id)
    {

        $user = auth()->user()->id;
        $db = Container::where('name', $id)->firstOrFail();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }
                
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
        $stream = ssh2_exec($connection, 'prlctl list -i '.$id.' -j');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result = stream_get_contents($stream_out);  
        $responseData = json_decode($result, true);

        return view('rebuild', ['data' => $responseData[0]]);
        
    }
    public function all()
    {
        $db = Container::all();
        return response($db, 200)->header('Access-Control-Allow-Origin', '*');
    }
}
