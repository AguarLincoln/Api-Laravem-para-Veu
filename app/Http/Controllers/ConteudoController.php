<?php

namespace App\Http\Controllers;

use App\Conteudo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConteudoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $user = $request->user();
        $conteudos = Conteudo::with('user')->orderBy('data_link','DESC')->paginate(5);
        foreach($conteudos as $conteudo){
            $conteudo->total_curtidas = $conteudo->curtidas()->count();  
            $curtiu = $user->curtidas()->find($conteudo->id);

            if($curtiu){
                $conteudo->curtiu_conteudo = true;
            }else{
                $conteudo->curtiu_conteudo = false;
            }
        }

        return ['status' => true, 'conteudos' => $conteudos];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['data_link'] = date('d/m/Y H:i:s');
        $user = $request->user();
        $data['user_id'] = $user->id;

        $validacao = Validator::make($data, [
            'titulo' =>  'required',
            'texto' => 'required',            
        ]);

        if($validacao->fails()){
            return ['status'=> false, 'validacao' => true, 'erros' => $validacao->errors()];
        }

        //$conteudo = Conteudo::create($data);
        $user->conteudos()->create($data);

        $conteudos = Conteudo::with('user')->orderBy('data_link','DESC')->paginate(5);
        return ['status' => true, 'conteudos' => $conteudos];
        
    }

    public function curtir(Request $request, $id){
        $conteudo = Conteudo::find($id);

        if($conteudo){
            $user = $request->user();
            $user->curtidas()->toggle($conteudo->id);

            return ['status' => true, 'curtidas' => $conteudo->curtidas()->count(), 
                        'lista' => $this->index($request)];
        }else{
            return ['status' => true, 'erro' => 'conteúdo não exite!'];
        }
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
