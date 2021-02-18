<?php

namespace App\Tests\Panther;

use App\Entity\User;
use Symfony\Component\Panther\PantherTestCase;

class End2EndTest extends PantherTestCase
{
    private $client;
    private $crawler;
    private $form;
    private $entityManager;
    private $projectDirectory;
    private $passwordEncoder;

    private $test_pic_directory;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $this->projectDirectory = $container->get('kernel')->getProjectDir();
        $this->test_pic_directory= $this->projectDirectory."/public/images/test_profile/";
        $this->passwordEncoder = $container->get("security.user_password_encoder.generic");
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $this->client = static::createPantherClient();
        $this->crawler = $this->client->request('GET', '/login');
        $this->form = $this->crawler->selectButton('Sign in')->form();
        $this->makeSureDatabaseIsEmpty();
    }

    public function test_login_pictureUpload_pictureDelete()
    {
        $user = $this->createTestUser();
        $oldPicture = $user->getProfilePicture();

        $this->form['email']->setValue('grv_sh@yahoo.co.in');
        $this->form['password']->setValue('morePizza');
        $this->crawler = $this->client->submit($this->form);
        $this->assertPageTitleContains("Dashboard");
        $this->form = $this->crawler->selectButton('Submit')->form();
        $this->form['profile[age]']->setValue("10");
        $this->form['profile[gender]']->setValue("0");
        $fileFormField = $this->form['profile[profilePicture]'];
        if (str_contains($oldPicture==null?"":$oldPicture, 'mickey'))
           $fileFormField->upload($this->test_pic_directory."donald.jpg");
        else
            $fileFormField->upload($this->test_pic_directory."mickey.jpg");

        //$this->assertInstanceOf(FileFormField::class, $fileFormField);
        $crawler=$this->client->submit($this->form);

        //2nd test: confirm the file has been changed in database
        $this->entityManager->refresh($user);
        $newPicture=$user->getProfilePicture();
        $this->assertFalse($oldPicture==$newPicture);

        //3rd test: new file's existence in expected folder
        $this->assertTrue(file_exists($this->projectDirectory.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."images".
            DIRECTORY_SEPARATOR."profile".DIRECTORY_SEPARATOR.$newPicture));

        //4th test: Delete picture with JavaScript confirm options
        $webDriver = $this->client->getWebDriver();
        try{
            $this->client->clickLink('Delete Picture');
        }
        catch (\Facebook\WebDriver\Exception\UnexpectedAlertOpenException $e)
        {
            //The execution of this code block means a prompt against delete
            $uri = $crawler->selectLink('Delete Picture')->link()->getUri();
            $this->client->request("GET",$uri);
        }
        $this->entityManager->refresh($user);
        $newPicture=$user->getProfilePicture();
        $this->assertTrue(null==$newPicture);

        $this->deleteTestUser($user);
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
        $user->setFirstName("Gourab");
        $user->setLastName("Sahu");
        $user->setGender("Male");
        $user->setAge("31");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteTestUser($user)
    {
        $picture = $this->projectDirectory.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."images".
            DIRECTORY_SEPARATOR."profile".DIRECTORY_SEPARATOR.$user->getProfilePicture();
        if($user->getProfilePicture())
        {
            chmod($picture, 0644);
            unlink($picture);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function makeSureDatabaseIsEmpty()
    {
        $users =$this->entityManager->getRepository(User::class)->findAll();
        foreach($users as $user)
        {
            $user = $this->entityManager->merge($user);
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
        $this->client->quit();
    }
}