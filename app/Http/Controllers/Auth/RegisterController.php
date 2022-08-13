<?php

namespace App\Http\Controllers\Auth;

//　↓ モデル
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // ↓ ログイン前でもアクセスを可能とする記述('guest')【佐藤追記】
    public function __construct()
    {
        // ↓('auth')　でログイン後でないと入れない【佐藤下記変更】
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    //バリデーション
    public function Validator(array $data)
    {
        $validator = Validator::make($data, [
            // $rules = [
            // //ルールを定義
            'username' => 'required|string|max:12|min:2',
            'mail' => 'required|string|email|min:5|max:40|unique:username,mail',
            //alpha_num...半角英数字
            'password' => 'required|string|min:8|max:20|confirmed|alpha_num',
            'password_confirmation' => 'required|same:password',
        ]);
        return $validator;
        // $validator = Validator::make($request->all(), $rules);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    // ↓ 新規登録データを格納
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'mail' => $data['mail'],
            'password' => bcrypt($data['password']),
        ]);
    }


    // public function registerForm()
    // {
    //     return view("auth.register");
    // }
    // ↓ 新規登録データを格納//入力
    public function register(Request $request)
    {
        // ↓リクエストメソッドが指定のものか確認【佐藤追記】
        if ($request->isMethod('post')) {
            //dataメソッドを追加
            $data = $request->input();
            $validator = $this->Validator($data);
            // dd($validator);
            if ($validator->fails()) {
                return redirect('/register')
                    ->withErrors($validator)
                    ->withInput();
            }

            //createメソッドを実行
            $this->create($data);
            // ↓ Controllerでwithで名前が出るように指示
            return redirect('added')->with('UserName', $data['username']);
        }
        return view('auth.register');
    }
    // ↓多分、if以外は新規登録画面に戻れ【佐藤追記】

    public function added(Request $request)
    {
        return view('auth.added');
    }
}
