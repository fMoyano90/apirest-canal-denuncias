<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Denuncia;
use App\Helpers\JwtAuth;

class DenunciaController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['store', 'upload', 'getFile']]);
    }

    public function index()
    {
        $denuncias = Denuncia::orderBy('created_at', 'desc')->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'denuncias' => $denuncias
        ], 200);
    }

    public function pendientes()
    {
        $denuncias = Denuncia::where('finalizada', 0)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'denuncias' => $denuncias
        ], 200);
    }

    public function getByCategoria($categoria)
    {
        switch ($categoria) {
            case 'transparencia':
                $denuncias = Denuncia::where('motivo', 'Ley de transparencia')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
            case 'laboral':
                $denuncias = Denuncia::where('motivo', 'Relaciones laborales')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
            case 'seguridad':
                $denuncias = Denuncia::where('motivo', 'Seguridad')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
            case 'ambiente':
                $denuncias = Denuncia::where('motivo', 'Medio ambiente')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
            case 'comunidad':
                $denuncias = Denuncia::where('motivo', 'Comunidad')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
            case 'disconformidad':
                $denuncias = Denuncia::where('motivo', 'Disconformidad con el servicio')->orderBy('created_at', 'desc')->get();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'denuncias' => $denuncias
                ], 200);
                break;
        }
    }

    public function show($id)
    {
        $denuncia = Denuncia::find($id);
        if (is_object($denuncia)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'denuncias' => $denuncia
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La denuncia no existe'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        // Recoger datos por post 
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Validar datos 
            $validate = \Validator::make($params_array, [
                'ticket' => 'required|unique:denuncias',
                'nombre' => 'required',
                'email' => 'required|email',
                'telefono' => 'required',
                'motivo' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la denuncia, faltan datos.',
                );
            } else {
                // Guardar articulo 
                $denuncia = new Denuncia();
                $denuncia->ticket           = $params->ticket;
                $denuncia->nombre           = $params->nombre;
                $denuncia->email            = $params->email;
                $denuncia->telefono         = $params->telefono;
                $denuncia->motivo           = $params->motivo;
                $denuncia->denuncia         = $params->denuncia;
                $denuncia->reclamo          = $params->reclamo;
                $denuncia->antecedentes     = $params->antecedentes;
                $denuncia->razon_social     = $params->razon_social;
                $denuncia->rut              = $params->rut;
                $denuncia->departamento     = $params->departamento;
                $denuncia->correo_encargado = $params->correo_encargado;
                $denuncia->correo_encargado2 = $params->correo_encargado2;
                $denuncia->finalizada = $params->finalizada;

                $denuncia->save();

                $data = array(
                    'code' => 200,
                    'status' => 'Success',
                    'denuncia' => $denuncia
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'messge' => 'Envia los datos correctamente.'
            );
        }

        // Devolver resultado 
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        // Recoger los datos por post 
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Datos enviados incorrectos'
        );

        if (!empty($params_array)) {
            // Validar datos 
            $validate = \Validator::make($params_array, [
                'nombre' => 'required',
                'telefono' => 'required',
                'motivo' => 'required',
            ]);

            if ($validate->fails()) {
                $data['errors'] =  $validate->errors();
                return response()->json($data, $data['code']);
            }

            // Eliminar los que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['ticket']);
            unset($params_array['email']);
            unset($params_array['created_at']);

            // Actualizar el registro en concreto 
            $denuncia = Denuncia::where('id', $id)->update($params_array);

            // Devolver algo 
            $data = array(
                'code' => 200,
                'status' => 'success',
                'denuncia' => $denuncia,
                'cambios' => $params_array
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
        // Comprobar si existe el registro
        $denuncia = Denuncia::find($id);
        $denuncia->delete();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'denuncia' => $denuncia
        );

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        // Recoger la imafen de la petición 
        $file = $request->file('file0');

        // Validar archivo
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|file'
        ]);

        // Guardar archivo 
        if (!$file || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Elige un archivo valido.'
            ];
        } else {
            $file_name = time() . $file->getClientOriginalName();

            \Storage::disk('denuncias')->put($file_name, \File::get($file));


            $data = [
                'code' => 200,
                'status' => 'success',
                'file' => $file_name
            ];
        }

        // Devolver datos 
        return response()->json($data, $data['code']);
    }

    public function getFile($filename)
    {
        // Comprobar si existe el fichero 
        $isset = \Storage::disk('denuncias')->exists($filename);

        if ($isset) {
            // Conseguir el archivo 
            $file = \Storage::disk('denuncias')->download($filename);
            // Devolver la imagen 
            return $file;
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El archivo no existe.'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
