<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordEncoder;

    public function setUp()
    {
        $this->client = static::createClient();
        parent::setUp(); // TODO: Change the autogenerated stub
        self::bootKernel();
        $container = self::$container;
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
        $this->passwordEncoder = $container->get("security.user_password_encoder.generic");
        $crawler= $this->client->request('GET', '/dashboard');
    }

    public function testUnauthorizedLogin()
    {
        $this->client->request('GET', '/dashboard');
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider
     */
    public function testPictureUpload()
    {
        //Do a fast-login
        $user1=$this->createTestUser();
        $this->client->loginUser($user1);
        $oldPicture = $user1->getProfilePicture();

        /* 3 things to test when uploading a picture or a file
          1st : A successful response from end-point
          2nd: The respective entity has stored file's name in database
          3rd: The file/ picture exists in the expected folder
         These 3 tests should be executed in chronological order so that we can
         find out which failure is actually responsible if something goes wrong.
        */

        $crawler = $this->client->request('GET','/dashboard');
        $form = $crawler->selectButton('Submit')->form();

        //These form-fields are mandatory fields and thats why we need set them
        $form["profile[firstName]"]->setValue('Gourab');
        $form["profile[lastName]"]->setValue('Sahu');
        $form["profile[gender]"]->setValue(0);
        $form["profile[age]"]->setValue(31);
        $form["profile[profilePicture]"]->upload($this->client->getKernel()->getProjectDir()."/public/images/test_profile/mickey.JPG");
        $this->client->submit($form);

        //1st test check response
        $this->assertResponseRedirects();

        //2nd test confirm the file has been changed in database
        $newPicture = self::$container->get(UserRepository::class)->findOneByEmail("grv_sh@yahoo.co.in")->getProfilePicture();
        /*$this->assertFalse($oldPicture==$newPicture);*/

        //3rd test new file's existence in expected folder
        $this->assertTrue(file_exists($this->client->getKernel()->getProjectDir()."/public/images/profile/".$newPicture));

        /* Test commit*/

    }


    public function testXmlHttpRequest()
    {
        $user1 = self::$container->get(UserRepository::class)->findOneByEmail("grv_sh@yahoo.co.in");
        $this->client->loginUser($user1);
        $id = $user1->getId();
        $this->client->xmlHttpRequest("POST","/whoami",['id'=>$id]);
        $data = json_decode($this->client->getResponse()->getContent(),true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($data['firstName']=="Gourab");

        //Logout user and check if getting an error
        $this->client->request("GET","/logout");
        $this->client->xmlHttpRequest("POST","/whoami",['id'=>4]);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(),true);
        $this->assertArrayHasKey('error',$data);

        //Remove the user after successful tests
        $this->deleteTestUser($user1);
    }

    public function createTestUser()
    {
        $user = new User();
        $user->setEmail("grv_sh@yahoo.co.in");
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'morePizza'
            )
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteTestUser($user)
    {
        unlink($this->client->getKernel()->getProjectDir()."/public/images/profile/".$user->getProfilePicture());
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

}