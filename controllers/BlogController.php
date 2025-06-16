<?php
/**
 * Contrôleur pour la gestion du blog
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Blog.php';

class BlogController extends Controller {
    private $blogModel;
      public function __construct() {
        parent::__construct();
        $this->blogModel = new Blog($this->db);
    }
      /**
     * Afficher la liste des billets
     */
    public function showBlog() {
        $search = isset($_GET['search']) ? $this->sanitize($_GET['search']) : null;
        $posts = $this->blogModel->getAllPosts($search);
        
        $this->render('blog/blog', [
            'pageTitle' => 'Blog',
            'posts' => $posts,
            'search' => $search
        ]);
    }
      /**
     * Afficher un billet et ses commentaires
     */
    public function showPost() {
        $post_id = (int)$_GET['id'];
        $post = $this->blogModel->getPostById($post_id);
          if(!$post) {
            $this->redirect('index.php?url=blog', 'Billet introuvable.', 'error');
            return;
        }
        
        $comments = $this->blogModel->getPostComments($post_id);
        
        $this->render('blog/post', [
            'pageTitle' => htmlspecialchars($post['titre']),
            'post' => $post,
            'comments' => $comments
        ]);
    }
      /**
     * Ajouter un commentaire
     */
    public function addComment() {
        $this->requireAuth();
          if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = (int)$_POST['post_id'];
            $content = $this->sanitize($_POST['content']);
              if(empty($content)) {
                $this->redirect('index.php?url=blog&action=post&id=' . $post_id, 'Le commentaire ne peut pas être vide.', 'error');
                return;
            }
            
            // Limiter la longueur du commentaire
            if(strlen($content) > 1000) {
                $this->redirect('index.php?url=blog&action=post&id=' . $post_id, 'Le commentaire est trop long (1000 caractères maximum).', 'error');
                return;
            }
            
            if($this->blogModel->addComment($post_id, $_SESSION['user_id'], $content)) {
                $this->redirect('index.php?url=blog&action=post&id=' . $post_id, 'Commentaire ajouté avec succès !', 'success');
            } else {
                $this->redirect('index.php?url=blog&action=post&id=' . $post_id, 'Erreur lors de l\'ajout du commentaire.', 'error');
            }
        }
    }
      /**
     * Créer un nouveau billet (admin seulement)
     */
    public function createPost() {
        $this->requireAdmin();
          if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $this->sanitize($_POST['title']);
            $content = $this->sanitize($_POST['content']);
              if(empty($title) || empty($content)) {
                $this->redirect('index.php?url=admin', 'Le titre et le contenu sont obligatoires.', 'error');
                return;
            }
              if($this->blogModel->createPost($title, $content, $_SESSION['user_id'])) {
                $this->redirect('index.php?url=blog', 'Billet créé avec succès !', 'success');
            } else {
                $this->redirect('index.php?url=admin', 'Erreur lors de la création du billet.', 'error');
            }
        } else {
            $this->render('blog/create_post', [
                'pageTitle' => 'Créer un nouveau billet'
            ]);
        }
    }
}
?>
