<?php
namespace Produit\Controller;

use Produit\Model\ProduitTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Produit\Form\ProduitForm;
use Produit\Model\Produit;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class ProduitController extends AbstractActionController
{
    private $tableProduit;
    private $sessionManager;
    private $sessionContainer;

    public function __construct(ProduitTable $tableProduit, SessionManager $sessionManager)
    {

        $this->tableProduit = $tableProduit;
        $this->sessionManager = $sessionManager;
        // if (!isset($sessionContainer)){      //Ne fonctionnne pas pour le moment..
        //     $sessionContainer = new Container("panier");
        //     $sessionContainer->panier = [];
        // }
        if (!isset($_SESSION["panier"]))
            $_SESSION["panier"] = [];        
    }

    public function indexAction(){
        $tableProduits =  $this->tableProduit->fetchAll();
        return new ViewModel([
            'produits' => $tableProduits,
        ]);
    }

    public function panierAction(){
        $panier = $_SESSION["panier"];
        return new ViewModel([
            'produits' => $this->tableProduit->fetchAllFromBasket($panier)
        ]);
    }
    

    public function ajouterPanierAction(){
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('produit');
        }
        
        $_SESSION["panier"][] = $id;
        return $this->redirect()->toRoute('produit', ["action" => "panier"]);

    }

    public function retirerPanierAction(){

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('produit');
        }

        if (!in_array($id,$_SESSION["panier"]))
            $this->redirect()->toRoute('produit', ["action" => "panier"]);

        $indexProduit = array_search($id,$_SESSION["panier"]);
        if ($indexProduit !== null)
            array_splice($_SESSION["panier"], $indexProduit,1);
        return $this->redirect()->toRoute('produit', ["action" => "panier"]);

    }

    public function viderPanierAction(){
        $_SESSION["panier"] = [];
        return $this->redirect()->toRoute('produit', ["action" => "panier"]);
    }

    public function adminAction(){
        $tableProduits =  $this->tableProduit->fetchAll();
        return new ViewModel([
            'produits' => $tableProduits,
        ]);
    }

    public function descriptionAction(){

    }

    public function addAction()
    {
        $form = new ProduitForm();
        $form->get('submit')->setValue('Ajouter');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $produit = new Produit();
        $form->setInputFilter($produit->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $produit->exchangeArray($form->getData());
        $this->tableProduit->saveProduit($produit);
        return $this->redirect()->toRoute('produit', ["action" => 'admin']);
    }

    public function editAction()
    {
        // $id = (int) $this->params()->fromRoute('id', 0);

        // if (0 === $id) {
        //     return $this->redirect()->toRoute('album', ['action' => 'add']);
        // }

        // // Retrieve the album with the specified id. Doing so raises
        // // an exception if the album is not found, which should result
        // // in redirecting to the landing page.
        // try {
        //     $album = $this->tableProduit->getAlbum($id);
        // } catch (\Exception $e) {
        //     return $this->redirect()->toRoute('album', ['action' => 'index']);
        // }

        // $form = new AlbumForm();
        // $form->bind($album);
        // $form->get('submit')->setAttribute('value', 'Edit');

        // $request = $this->getRequest();
        // $viewData = ['id' => $id, 'form' => $form];

        // if (! $request->isPost()) {
        //     return $viewData;
        // }

        // $form->setInputFilter($album->getInputFilter());
        // $form->setData($request->getPost());

        // if (! $form->isValid()) {
        //     return $viewData;
        // }

        // $this->tableProduit->saveAlbum($album);

        // // Redirect to album list
        // return $this->redirect()->toRoute('album', ['action' => 'index']);
    }

    public function deleteAction()
    {
    //     $id = (int) $this->params()->fromRoute('id', 0);
    //     if (!$id) {
    //         return $this->redirect()->toRoute('album');
    //     }

    //     $request = $this->getRequest();
    //     if ($request->isPost()) {
    //         $del = $request->getPost('del', 'No');

    //         if ($del == 'Yes') {
    //             $id = (int) $request->getPost('id');
    //             $this->tableProduit->deleteAlbum($id);
    //         }

    //         // Redirect to list of albums
    //         return $this->redirect()->toRoute('album');
    //     }

    //     return [
    //         'id'    => $id,
    //         'album' => $this->tableProduit->getAlbum($id),
    //     ];
    }
}