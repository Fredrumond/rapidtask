<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeTest extends TestCase
{

    //CRIANDO UM USUARIO PARA O TESTE
    public function usuario()
    {
        return $this->criaUsuario();
    }    
      
    /**    
     * @test
     */
    public function listar_times()
    {
        $response = $this->actingAs($this->usuario())->get('admin/times')->assertStatus(200)->assertViewHas('times');       
    }

    /**    
     * @test
     */
    public function criar_time()
    {        
        $response = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ])->assertStatus(201);       

    }

    /**    
     * @test
     */
    public function editar_time()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ]);

        $responseTime = json_decode($time->getContent());
        
        $response = $this->actingAs($this->usuario())->post('admin/time/editar',[
            'time_id' => $responseTime->id,
            'nome' => 'Time Teste Update do ' .  $this->usuario()->name,
            'logo' => ''         
        ])->assertStatus(200);

    }

    /**    
     * @test
     */
    public function ver_time()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ]);

        $responseTime = json_decode($time->getContent());        

        $response = $this->actingAs($this->usuario())->get('admin/time/ver/'.$responseTime->id)->assertStatus(200);
        $response->assertViewHas('time');                      
        $response->assertViewHas('timeMembro');

    }

    /**    
     * @test
     */
    public function ver_time_que_nao_existe()
    {
        $response = $this->actingAs($this->usuario())->get('admin/time/ver/'.-1)->assertStatus(404);
    }

    /**    
     * @test
     */
    public function excluir_time()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ]);

        $responseTime = json_decode($time->getContent());            

        $response = $this->actingAs($this->usuario())->get('admin/time/excluir/'.$responseTime->id)->assertStatus(200);       
    }
}
