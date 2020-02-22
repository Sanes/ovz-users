<?php

namespace App\Http\Controllers;
use App\Container; 
use App\User; 
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
        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl list -a -o status,name,hostname,ip_configured,description -j');
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

        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl list -i '.$id.' -j');    
        $responseData = json_decode($result, true);
        $resultStat = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl exec '.$id.' /usr/local/bin/monit'); 
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
               
        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl list -i '.$id.' -j');
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
        if ($request['password']) {
            $passwduser = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 12);
            $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname'].' --userpasswd root:'.$passwduser);
            \Session::flash('pwgen', $passwduser);            
        } 
        else {
            $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl set '.$request['name'].' --description "'.$request['description'].'" --hostname '.$request['hostname']);
        }

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
        $db = Container::where('name', $id)->first();
        if ($db->user_id != auth()->user()->id) {
            return redirect('/ct');
        }        
        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl '.$action.' '.$id.' > /dev/null &');
        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl reset-uptime '.$id);
        if ($action == "start") {
            $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl set '.$id.' --autostart on');
        }
        elseif ($action == "stop") {
            $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl set '.$id.' --autostart off');
        }
        else {
            $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl set '.$id);
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
                
        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl list -i '.$id.' -j');
        $responseData = json_decode($result, true);

        return view('rebuild', ['data' => $responseData[0]]);
        
    }
}
