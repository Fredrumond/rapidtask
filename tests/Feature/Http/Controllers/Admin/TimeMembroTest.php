<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeMembroTest extends TestCase
{

    //CRIANDO UM USUARIO PARA O TESTE
    public function usuario()
    {
        return $this->criaUsuario();
    }    
      
    /**    
     * @test
     */
    public function convida_membro_ja_na_plataforma()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ])->assertStatus(201);

        $responseTime = json_decode($time->getContent());        

        $usuario = $this->criaUsuario();

        $conviteResponse = $this->actingAs($this->usuario())->post('admin/time-membro/novo',[
            'email' => $usuario->email,
            'time' => $responseTime->id            
        ])->assertStatus(201);

    }

    /**    
     * @test
     */
    public function convida_membro_novo()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ])->assertStatus(201);

        $responseTime = json_decode($time->getContent());

        $conviteResponse = $this->actingAs($this->usuario())->post('admin/time-membro/novo',[
            'nome' => 'Frederico Drumond',
            'email' => 'fredrumond@gmail.com',
            'time' => $responseTime->id            
        ])->assertStatus(201);

    }

    

    
}
