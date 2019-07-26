<?php

namespace App\Tests\form;

use App\Entity\User;
use App\Form\UserForgotPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserForgotPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $token = 'token';
        $formData = [
            'email' => 'test@email.com',
        ];

        $objectToCompare = new User();
        $objectToCompare->setToken($token);
        // 1) verify if the FormType compiles
        $form = $this->factory->create(UserForgotPasswordType::class, $objectToCompare);

        $object = new User();
        $object->setEmail($formData['email']);
        $object->setToken($token);
        $form->submit($formData);

        // 2) This test checks that none of your data transformers used by the form failed.
        $this->assertTrue($form->isSynchronized());

        // 3) The test below checks if all the fields are correctly specified
        $this->assertEquals($object, $objectToCompare);

        // 4) check if all widgets to display are available
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}