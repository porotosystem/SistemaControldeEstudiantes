<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Pagos;
use App\Models\Cursos;
use App\Models\Alumnos;
use Illuminate\Http\Request;

class PagosController extends Controller
{

    public function index()
    {
        $pagos = Pagos::orderBy('id', 'DESC')->paginate(6);
        return view('pagos.index', compact('pagos'));       
    }


    public function create()
    {

        $cursos = Cursos::all();
        $alumnos = Alumnos::all();
        return view('pagos.add', compact('alumnos','cursos'));   
    }


    public function store(Request $request)
    {
        if($request->ajax()){
    
            $datosAlumno = Alumnos::with('curso')->where("id",$request->alumno_id)->first();
            $idCursoAlumno = $datosAlumno->curso_id;
            // dd($datosAlumno);
            $curso = $datosAlumno->curso->nombre_curso;

            $valorCurso = DB::table('cursos')->where("id",$request->alumno_id)->get();

            $pagosCurso = DB::table('pagos')
            ->where("alumno_id",$request->alumno_id)
            ->sum('aporte');

           
            return response()->json([
                'infoAlumno'=> $curso,
                'infoAlumnoIdCurso' => $idCursoAlumno,
                'infoCurso'=>$valorCurso,
                'infoPagosCurso' => $pagosCurso
            ]);  
        }

    }


    public function guardarPago(Request $request)
    {

        if ($request->hasFile('photo_pago')) {
            $file = $request->file('photo_pago');  
            $nombrearchivo = time()."_".$file->getClientOriginalName();  
            $file->move(public_path('/fotosPagos/'),$nombrearchivo); 

            $data = new Pagos([
                'alumno_id'=>$request->get('alumno_id'),
                'curso_id'=>$request->get('curso_id'),
                'valor_curso'=>$request->get('valor_curso'),
                'aporte'=>$request->get('aporte'),
                'photo_pago'=>$nombrearchivo
            ]);
            $data->save(); 
        }else{
            $data = new Pagos([
                'alumno_id'=>$request->get('alumno_id'),
                'curso_id'=>$request->get('curso_id'),
                'valor_curso'=>$request->get('valor_curso'),
                'aporte'=>$request->get('aporte')
            ]);
            $data->save(); 
        }

        
        $pagos = Pagos::orderBy('id', 'DESC')->paginate(3);
        return view('pagos.index', compact('pagos'));    
    }

    public function show(Request $request)
    {
        //
    }


    public function exportPagosAlumnos()
    {
        $pagos = Pagos::all();
        return view('exports.exportPagos', compact('pagos'));        

    }


    public function edit(Pagos $pagos)
    {
        //
    }


    public function update(Request $request, Pagos $pagos)
    {
        //
    }

  
    public function destroy(Pagos $pagos)
    {
        //
    }
}
