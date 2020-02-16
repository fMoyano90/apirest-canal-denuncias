<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Contacto;
use App\Helpers\JwtAuth;

class ContactoController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['store', 'upload', 'getFile']]);
    }

    public function index()
    {
        $contactos = Contacto::paginate(10);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'contactos' => $contactos
        ], 200);
    }

    public function show($id)
    {
        $contacto = Contacto::find($id);
        if (is_object($contacto)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'contactos' => $contacto
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El registro del contacto no existe.'
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
                $contacto = new Contacto;
                $contacto->nombre = $params->nombre;
                $contacto->email = $params->email;
                $contacto->telefono = $params->telefono;
                $contacto->motivo = $params->motivo;
                $contacto->cargo = $params->cargo;
                $contacto->pretension_renta = $params->pretension_renta;
                $contacto->curriculum = $params->curriculum;
                $contacto->rut = $params->rut;
                $contacto->empresa = $params->empresa;
                $contacto->razon_social = $params->razon_social;
                $contacto->producto_proveedor = $params->producto_proveedor;
                $contacto->servicio_solicitado = $params->servicio_solicitado;
                $contacto->tipo_equipo = $params->tipo_equipo;
                $contacto->origen = $params->origen;
                $contacto->destino = $params->destino;
                $contacto->carga = $params->carga;
                $contacto->asunto = $params->asunto;
                $contacto->mensaje = $params->mensaje;

                $contacto->save();

                $data = array(
                    'code' => 200,
                    'status' => 'Success',
                    'contacto' => $contacto
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Envía los datos correctamente.'
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
            unset($params_array['motivo']);
            unset($params_array['created_at']);

            // Actualizar el registro en concreto 
            $contacto = Contacto::where('id', $id)->update($params_array);

            // Devolver algo 
            $data = array(
                'code' => 200,
                'status' => 'success',
                'denuncia' => $contacto,
                'cambios' => $params_array
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
        // Comprobar si existe el registro
        $contacto = Contacto::find($id);
        $contacto->delete();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'denuncia' => $contacto
        );

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        // Recoger la imafen de la petición 
        $file = $request->file('file0');

        // Validar imagen 
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|file'
        ]);

        // Guardar la imagen 
        if (!$file || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Elige un archivo valido.'
            ];
        } else {
            $file_name = time() . $file->getClientOriginalName();

            \Storage::disk('contactos')->put($file_name, \File::get($file));


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
        $isset = \Storage::disk('contactos')->exists($filename);

        if ($isset) {
            // Conseguir el archivo
            $file = \Storage::disk('contactos')->download($filename);
            // Devolver el archivo
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
