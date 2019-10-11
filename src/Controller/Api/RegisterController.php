<?php
namespace App\Controller\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Register;
use App\Dto\RegisterDto;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

use AutoMapperPlus\AutoMapperInterface;


/**
 * @Route("/record")
 */
class RegisterController extends AbstractController
{

    private $mapper;
    function __construct(AutoMapperInterface $mapper){
        $this->mapper = $mapper;
    }

    /**
     * @Route("/",  methods={"GET"})
     *
     */
    public function getAll(){        
        $em = $this->getDoctrine()->getManager();
        
        try {
            $code = 200;
            $error = false;
            
            $Register = $em->getRepository("App:Register")->findAll();           
            $mapped = $this->mapper->mapMultiple($Register, RegisterDto::class);

            if (is_null($Register)) {
                $code = 500;
                $error = true;
                $message = "The Registers does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the Register List - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $mapped : $message,
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}",  methods={"GET"})
     *
     */
    public function getById(Request $request,int $id){        
        $em = $this->getDoctrine()->getManager();
        
        try {
            $code = 200;
            $error = false;
            
            $Register = $em->getRepository("App:Register")->find($id);   
            $mapped = $this->mapper->map($Register, RegisterDto::class);         

            if (is_null($Register)) {
                $code = 500;
                $error = true;
                $message = "The Register does not exist";
            }
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the Register List - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $mapped : $message,
        ];        

        return new JsonResponse($response);
    }

    /**
     * @Route("/",  methods={"POST"})
     *
     */
    public function setRegister(Request $request){        

        $data = json_decode(
            $request->getContent(),
            true
        );              

        $message = "";
        $Register = $mapped = [];

        try {
            $code = 201;
            $error = false;
            
            if($this->validate($data)){            
                $Register = new Register();             
                $this->toRecord($Register, $data, 'new');
                $mapped = $this->mapper->map($Register, RegisterDto::class);  
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new Register - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 201 ? $mapped : $message,
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}",  methods={"PUT"})
     *
     */
    public function updateRegister(Request $request, int $id){
        $em = $this->getDoctrine()->getManager();
        $data = json_decode(
            $request->getContent(),
            true
        );        
        
        $message = "";
        $Register = $mapped = [];

        try {
            $code = 200;
            $error = false;
            
            if($this->validate($data)){
                $Register = $em->getRepository("App:Register")->find($id);            
                $this->toRecord($Register, $data, 'update');
                $mapped = $this->mapper->map($Register, RegisterDto::class);  
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new Register - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $mapped : $message,
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}",  methods={"DELETE"})
     *
     */
    public function deleteRegister(Request $request, int $id){
        
        $em = $this->getDoctrine()->getManager();

        try {
            $code = 200;
            $error = false;
            $Register = $em->getRepository("App:Register")->find($id);

            if (!is_null($Register)) {
                $em->remove($Register);
                $em->flush();

                $message = "The Register was removed successfully!";

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent Register - Error: The Register id does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current Register - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];

        return new JsonResponse($response);
    }

    private function validate($data){

        $validator = Validation::createValidator();
        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'firstname' => new Assert\Length(array('min' => 1)),
            'lasttname' => new Assert\Length(array('min' => 1)),            
            'email' => new Assert\Email(),
        ));
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 500);
        }
        return true;
    }

    private function toRecord(Register &$Register, $data,  $type){

        $em = $this->getDoctrine()->getManager();

        $Register->setFirstname($data['firstname']);
        $Register->setLastname($data['lastname']);
        $Register->setEmail($data['email']);
        $Register->setPhone($data['phone']);
        $Register->setAddress($data['address']);
        $Register->setOthers($data['others']);

        if($type == 'update'){
            $Register->setActive($data['active']);
            $Register->setUpdatedAt(new \DateTime());
        }

        if($type == 'new'){
            $Register->setActive(0);
            $Register->setCreatedAt(new \DateTime());    
        }

        $em->persist($Register);
        $em->flush();
    }


    /* 
        TODO         
        - Relevant doc
    */

}
