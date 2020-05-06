<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactSupportTest extends  TestCase
{
    /** @test */
    function it_send_a_support_email()
    {
        Mail::fake();

        $this->post('/contact', $fields = $this->validFields(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        Mail::assertQueued(SupportTickket::class, function ($mail) use ($fields)
            return $mail->sender == $fields['email'];
    });
}

    /** @test */
    function it_requires_a_name()
    {

        $this->contact(['name' =>''])
            ->assertSessionHasErrors('name');

    }

    /** @test */
    function it_requires_a_valid_email()
    {
        $this->contact(['email' =>'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    function it_requires_a_question()
    {
        $this->contact(['question' =>''])
            ->assertSessionHasErrors('question');
    }

    /** @test */
    function it_requires_a_verification()
    {
        $this->contact(['verification' =>''])
            ->assertSessionHasErrors('verification');
    }

    /** @test */
    function it_requires_a_correct_verification_for_1_plus_4()
    {
        $this->contact(['verification' => 0])
            ->assertSessionHasErrors('verification');

        Mail::fake();

        $this->contact(['verification' => 5])
            ->assertSessionHasErrors('verification' => 'five');

        Mail::assertQueued(SupportTicket::class, 2);
    }

    protected function contact($attributes = [])
    {
        $this->withExceptionHandling();

        return $this->post('/contact', $this->validFields($attributes));
    }


    /**
     * @return array
     */
    protected function validFields($overrides = [])
    {
        return array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'question' => 'Help me',
            'verification' => 5
        ], $overrides);
    }
}
