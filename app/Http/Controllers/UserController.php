<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function index()
    {
        $users = User::get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'usuarios' => $users
        ], 200);
    }

    public function register(Request $request)
    {
        // Recoger los datos del usuario por post 
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array) && !empty($params)) {
            //Limpiar datos 

            $params_array = array_map('trim', $params_array);

            // Validar los datos 
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'role' => 'required|alpha',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                // Cifrar contaseña
                $pwd = hash('sha256', $params->password);

                // Crear usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->role = $params_array['role'];
                $user->password = $pwd;

                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',
            );
        }

        return response()->json($data, $data['code']);
    }
    public function login(Request $request)
    {
        $jwtAuth = new \JwtAuth();

        // Recibir datos por POST 
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Validar los datos 
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            // Cifrar password
            $pwd = hash('sha256', $params->password);
            // Devolver token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);

            if (!empty($params->gettoken)) {
                $signup =  $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
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
                'role'    => 'required'
            ]);
            if ($validate->fails()) {
                $data['errors'] =  $validate->errors();
                return response()->json($data, $data['code']);
            }
            // Eliminar los que no queremos actualizar
            unset($params_array['created_at']);
            unset($params_array['id']);

            // Actualizar el registro en concreto 
            $usuario = User::where('id', $id)->update($params_array);

            // Devolver algo 
            $data = array(
                'code' => 200,
                'status' => 'success',
                'usuario' => $usuario,
                'cambios' => $params_array
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        // Recoger datos de la petición 
        $image =  $request->file('file0');

        // Validación de imagen 
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Guardar imagen 
        if (!$image || $validate->fails()) {
            //Devolver error
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen.'
            );
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            // Devolver resultado
            $data =  array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        $isset = \Storage::disk('users')->exists($filename);
        if ($isset) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'image' => 'La imagen no existe'
            );

            return response()->json($data, $data['code']);
        }
    }

       public function destroy($id, Request $request)
    {
        // Comprobar si existe el usuario
        $usuario = User::find($id);
        $usuario->delete();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'usuario' => $usuario
        );

        return response()->json($data, $data['code']);
    }
}
