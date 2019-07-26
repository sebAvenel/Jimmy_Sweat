<?php

namespace App\Tests\form;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = [
            'firstname' => 'testFirstname',
            'lastname' => 'testLastname',
            'phone' => '0123456789',
            'email' => 'test@email.com',
            'message' => ''
        ];

        $objectToCompare = new Contact();
        $form = $this->factory->create(ContactType::class, $objectToCompare);

        $object = new Contact();
        $object->setFirstname($formData['firstname']);
        $object->setLastname($formData['lastname']);
        $object->setPhone($formData['phone']);
        $object->setEmail($formData['email']);
        $object->setMessage($formData['message']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}