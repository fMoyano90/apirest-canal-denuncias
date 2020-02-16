<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Noticia;
use App\Helpers\JwtAuth;

class NoticiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage', 'getUltimasNoticias', 'getNoticiaPrincipal']]);
    }

    public function index()
    {
        $noticias = Noticia::where('principal', 0)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'noticias' => $noticias
        ], 200);
    }

    public function show($id)
    {
        $noticia = Noticia::find($id);
        if (is_object($noticia)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'noticias' => $noticia
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe'
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
            // Conseguir usuario identificado 
            $jwtAuth =  new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            // Validar datos 
            $validate = \Validator::make($params_array, [
                'categoria' => 'required',
                'titulo' => 'required',
                'cuerpo' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la noticia, faltan datos.'
                );
            } else {
                // Guardar articulo 
                $noticia = new Noticia();
                $noticia->categoria = $params->categoria;
                $noticia->titulo    = $params->titulo;
                $noticia->descripcion    = $params->descripcion;
                $noticia->cuerpo    = $params->cuerpo;
                $noticia->imagen    = $params->imagen;
                $noticia->principal = $params->principal;

                $noticia->save();

                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'noticia' => $noticia
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente.'
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
                'categoria' => 'required',
                'titulo'    => 'required',
                'cuerpo'    => 'required'
            ]);
            if ($validate->fails()) {
                $data['errors'] =  $validate->errors();
                return response()->json($data, $data['code']);
            }
            // Eliminar los que no queremos actualizar
            unset($params_array['created_at']);
            unset($params_array['id']);

            // Actualizar el registro en concreto 
            $noticia = Noticia::where('id', $id)->update($params_array);

            // Devolver algo 
            $data = array(
                'code' => 200,
                'status' => 'success',
                'noticia' => $noticia,
                'cambios' => $params_array
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
        // Comprobar si existe el registro
        $noticia = Noticia::find($id);
        $noticia->delete();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'noticia' => $noticia
        );

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request)
    {
        // Recoger la imafen de la petición 
        $image = $request->file('file0');

        // Validar imagen 
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Guardar la imagen 
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();

            \Storage::disk('imagenes')->put($image_name, \File::get($image));


            $data = [
                'code' => 200,
                'status' => 'success',
                'imagen' => $image_name
            ];
        }

        // Devolver datos 
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        // Comprobar si existe el fichero 
        $isset = \Storage::disk('imagenes')->exists($filename);

        if ($isset) {
            // Conseguir la imagen 
            $file = \Storage::disk('imagenes')->get($filename);
            // Devolver la imagen 
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getNoticiaPrincipal()
    {
        $noticia = Noticia::where('principal', 1)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return response()->json([
            'status' => 'success',
            'noticia' => $noticia
        ]);
    }

    public function getUltimasNoticias()
    {
        $noticias = Noticia::paginate(3)->sortByDesc('created_at');


        return response()->json([
            'status' => 'success',
            'noticias' => $noticias
        ]);
    }
}
