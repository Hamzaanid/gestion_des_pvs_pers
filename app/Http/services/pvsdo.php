<?php
namespace App\Http\services;
use App\Models\pvs;
use Illuminate\Support\Facades\DB;

use App\Models\pvsHasDataPartie;

use App\Http\services\upload_fichier;

class pvsdo{

    public static function create($request){
        //$request->pv = json_decode($request->pv, true);
        $insert =[
            'TypeSourcePvsID'=> $request->pv['TypeSourcePvsID'],
            'typepvsID'=> $request->pv['typepvsID'],
            'typePoliceJudicID'=>$request->pv['typePoliceJudicID'],
            'sujetpvs'=> $request->pv['sujetpvs'],
            'dateEnregPvs'=> $request->pv['dateEnregPvs'],
            'policeJudics'=> $request->pv['policeJudics'],
            'Numpvs' => $request->pv['Numpvs'],
            'datePvs' =>  $request->pv['datePvs'],
            'contreInnconue'=> $request->pv['contreInnconue']
        ];

                $pv = pvs::create($insert);

                $data[0]= $pv->id;
                $data[1]= $request ->pv['Numpvs'];
            return $data;
    }

    public static function update($request,$id){
        $pv =pvs::find($id);
        $pv->update([
            'TypeSourcePvsID'=> $request->pv['TypeSourcePvsID'],
            'typepvsID'=> $request->pv['typepvsID'],
            'sujetpvs'=> $request->pv['sujetpvs'],
            'dateEnregPvs'=> $request->pv['dateEnregPvs'],
            'policeJudics'=> $request->pv['policeJudics'],
            'typePoliceJudicID' =>$request->pv['typePoliceJudicID'],
            'Numpvs' => $request->pv['Numpvs'],
            'datePvs' =>  $request->pv['datePvs'],
            'heureRealisation'=> $request->pv['heureRealisation'],
            'contreInnconue'=> $request->pv['contreInnconue']
        ]);
   }

    public static function delete($id){
        $pv = pvs::find($id);
        if($pv){
            $pv->delete();
            return $pv->id." has been deleted.";
        }
        return "Not found.";
    }
    public static function getPvs_of_user($request){
        //les pvs des vice procurreur  automatique
               /*  return $pl = pvs::with('userhaspvs:id,dateMission,descision,traitID',
                                        'hasfichier:id,lien')
                 ->select('id','Numpvs','dateEnregPvs')
                 ->whereIn('id',function ($query) {
                         global $request;
                      $query->select('pvsID')
                          ->from('user_has_pvs')
                          ->where('user_has_pvs.userID',$request->userID)
                          ->where('user_has_pvs.traitID',1);
                      })->orderBy('created_at','desc')->get(); */

    return $pvs = DB::table('pvs')
         ->join('user_has_pvs', 'pvs.id', '=', 'user_has_pvs.pvsID')
         ->join('pvs_has_fichiers', 'pvs.id', '=', 'pvs_has_fichiers.pvsID')
         ->select( 'user_has_pvs.id as userhaspvsID', 'pvs.id', 'pvs.Numpvs', 'pvs.dateEnregPvs',
                    'user_has_pvs.dateMission','user_has_pvs.descision','user_has_pvs.traitID',
                    'pvs_has_fichiers.lien')
                    ->where('user_has_pvs.userID',$request->userID)
                    ->whereIn('user_has_pvs.traitID',[1,2])
                    ->get();


         }

    public static function get_pvs_betwDateEnrg($request){
        $cher = $request->cher;
        return $pv = pvs::with('typepvs','typesourcepvs','typepolicejudiciaire')
                    ->where('typepvsID',$cher['id_type'])
                    ->whereBetween('dateEnregPvs', [$cher['de'], $cher['a']])
                    ->whereNotIn('id',function ($query) {
                        $query->select('pvsID')->from('user_has_pvs');
                    })->get();
    }
    public static function stat($request){
        $cher = $request->cher;

        $pvs_non_traiter = DB::table('pvs')
        ->whereBetween('dateEnregPvs',[$cher['de'], $cher['a']]) //!!!
        ->whereNotIn('id',function ($query) {
                    global $request;
                 $query->select('pvsID')
                     ->from('user_has_pvs');
                    })
        ->count();

        $pvs_traiter = DB::table('pvs')
        ->whereBetween('dateEnregPvs',[$cher['de'], $cher['a']]) //!!!
        ->whereIn('id',function ($query) {
                    global $request;
                 $query->select('pvsID')
                     ->from('user_has_pvs')
                     ->where('user_has_pvs.traitID',2)
                     ->Orwhere('user_has_pvs.traitID',3);
                    })
        ->count();

        $pvs_cours_traiter =DB::table('pvs')
        ->whereBetween('dateEnregPvs',[$cher['de'], $cher['a']]) //!!!
        ->whereIn('id',function ($query) {
                    global $request;
                 $query->select('pvsID')
                     ->from('user_has_pvs')
                     ->where('user_has_pvs.traitID',1);
                    })
        ->count();

        return response()->json([
            'Traiter' => $pvs_traiter,
            'NonTraiter' => $pvs_non_traiter,
            'enCours' => $pvs_cours_traiter

        ],200);
    }
}
?>
