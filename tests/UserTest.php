<?php

namespace tests;
class UserTest extends \TestCase
{
    /**
     *  /api/login [POST}
     */
    public function testShouldCreateToken()
    {
        $parameters = [
            'username' => 'Shabnam97',
            'password' => 'Test123@',
        ];
        $this->post("api/login", $parameters, []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                'success',
                'response' => [
                'token'
                ]
            ]
        );
    }

    /**
     * /api/register [POST]
     */
    public function testShouldCreateUser()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $parameters = [
            'name' => 'Test',
            'username' => 'TestSur',
            'password' => 'Test123456',
            'email' => 'test@mail.ru',
            'date_of_birth' => '14-12-1948',
            'salary_min' => 400,
            'phone' => '+994513404328'
        ];
        $this->post("api/register", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                'success',
                'response',
                'data' => [
                    'id',
                    'image',
                    'name',
                    'username',
                    'gender',
                    'date_of_birth',
                    'education',
                    'experience',
                    'category',
                    'subcategory',
                    'wished_country',
                    'wished_city',
                    'salary_min',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ]
            ]
        );

    }


    /**
     * /api/forgotpassword/{id}[POST]
     */
    public function testShouldSendCodeBySMS()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $parameters = [
            'phone' => '+994513404328'
        ];
        $this->post("/api/forgotpassword/5",$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(

            [
                'success',
                'response',
            ]

        );

    }

    /**
     * /api/changepassword/5 [POST]
     */
    public function testShouldChangePassword()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $parameters = [
            'sms_code' => 'zz5Q',
            'new_pass' => 'Test123456',
            'new_pass_repeat' => 'Test123456',
        ];
        $this->post("/api/changepassword/5", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(

            [
                'success',
                'response',
            ]

        );

    }
    /**
     * /api/profile/48 [GET]
     */
    public function testShouldShowUserProfile()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];

        $this->get("/api/profile/48", [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(

            [
                'success',
                'response',
                'data' => [
                    'id',
                    'image',
                    'name',
                    'username',
                    'gender',
                    'date_of_birth',
                    'education',
                    'experience',
                    'category',
                    'subcategory',
                    'wished_country',
                    'wished_city',
                    'salary_min',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ]
            ]

        );

    }
    /**
     * /api/profile/48 [POST]
     */
    public function testShouldUploadUserImage()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $parameters = [
            'image' => 'dGVzdC5qcGc=',
        ];

        $this->post("/api/profile/48", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(

            [
                'success',
                'response',
            ]

        );

    }

    /**
     *  /api/subcategories/{id} [GET}
     */
    public function testShouldGetSubcategories()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $this->get("/api/subcategories/3", [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                'success',
                'response' => [

                ]
            ]
        );
    }

    /**
     *  /api/cities/{id} [GET}
     */
    public function testShouldGetCities()
    {
        $header = [
            'Authorization: Bearer ' . $this->testShouldCreateToken()
        ];
        $this->get("/api/cities/1", [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                'success',
                'response' => [

                ]
            ]
        );
    }
}