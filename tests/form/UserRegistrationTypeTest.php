<?php

namespace App\Tests\form;

use App\Entity\User;
use App\Form\UserRegistrationType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserRegistrationTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'nameTest',
            'email' => 'emailTest',
            'password' => 'passwordTest',
        ];

        $userToCompare = new User();

        $form = $this->factory->create(UserRegistrationType::class, $userToCompare);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}