<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;

class ClienteTest extends TestCase
{

    //CRIANDO UM USUARIO PARA O TESTE
    public function usuario()
    {
        return $this->criaUsuario();
    }    

    /**    
     * @test
     */
    public function listar_clientes()
    {
        $response = $this->actingAs($this->usuario())->get('admin/clientes')->assertStatus(200)->assertViewHas('clientes');       
    }

    /**    
     * @test
     */
    public function criar_cliente()
    {        
       $faker = Factory::create();

       $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
        'nome' => 'Time Teste do ' .  $this->usuario()->name            
    ])->assertStatus(201);   

       $responseTime = json_decode($time->getContent());  

       $response = $this->actingAs($this->usuario())->post('admin/cliente/salvar',[
        'nome' => $faker->name,
        'email' => $faker->email,
        'telefone' => '(31)99744-9090',      
        'time_id' => $responseTime->id 
    ])->assertStatus(201);  

   }

    
    public function editar_cliente()
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


    public function ver_time_que_nao_existe()
    {
        $response = $this->actingAs($this->usuario())->get('admin/time/ver/'.-1)->assertStatus(404);
    }


    public function excluir_time()
    {        
        $time = $this->actingAs($this->usuario())->post('admin/time/salvar',[
            'nome' => 'Time Teste do ' .  $this->usuario()->name            
        ]);

        $responseTime = json_decode($time->getContent());            

        $response = $this->actingAs($this->usuario())->get('admin/time/excluir/'.$responseTime->id)->assertStatus(200);       
    }
}
