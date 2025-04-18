package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.DatePicker;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import models.CategorieRessource;
import models.RessourceEducative;
import services.CategorieRessourceService;
import services.RessourceEducativeService;

import java.time.LocalDateTime;
import java.util.List;

public class RessourceEducativeController {

    @FXML
    private Button btnAjouter;

    @FXML
    private ComboBox<CategorieRessource> comboCategorie;

    @FXML
    private DatePicker datePublication;

    @FXML
    private DatePicker datePicker;

    @FXML
    private TextField txtAuteur;

    @FXML
    private TextArea txtContenu;

    @FXML
    private TextField txtImage;

    @FXML
    private TextField txtTitre;

    @FXML
    private TextField txtVideo;

    // Instance du service pour gérer les ressources et les catégories
    private RessourceEducativeService ressourceService = new RessourceEducativeService();
    private CategorieRessourceService categorieService = new CategorieRessourceService();

    public void initialize() {
        // Charger les catégories depuis la base de données
        List<CategorieRessource> categories = categorieService.getAllCategories();

        // Ajouter les catégories dans le ComboBox
        comboCategorie.getItems().addAll(categories);

        // Lier l'action au bouton Ajouter
        btnAjouter.setOnAction(event -> ajouterRessource());
    }

    // Méthode pour ajouter une nouvelle ressource
    // Méthode pour ajouter une nouvelle ressource
    private void ajouterRessource() {
        // Récupérer les données du formulaire
        String titre = txtTitre.getText();
        String contenu = txtContenu.getText();
        String auteur = txtAuteur.getText();
        String image = txtImage.getText();
        String video = txtVideo.getText();
        LocalDateTime datePublication = LocalDateTime.now();  // Utiliser la date actuelle

        // Récupérer la catégorie sélectionnée
        CategorieRessource categorie = comboCategorie.getSelectionModel().getSelectedItem();
        int categorieId = categorie != null ? categorie.getId() : -1; // ID de la catégorie

        // Afficher un message de log pour vérifier les valeurs avant l'ajout
        System.out.println("Ajout de la ressource :");
        System.out.println("Titre: " + titre);
        System.out.println("Contenu: " + contenu);
        System.out.println("Auteur: " + auteur);
        System.out.println("Image: " + image);
        System.out.println("Vidéo: " + video);
        System.out.println("Date de publication: " + datePublication);
        System.out.println("ID Catégorie: " + categorieId);

        // Créer une instance de RessourceEducative avec l'ID de la catégorie
        RessourceEducative ressource = new RessourceEducative(titre, contenu, auteur, image, video, datePublication, categorie);

        // Ajouter la ressource via le service
        ressourceService.ajouterRessource(ressource);

        // Réinitialiser les champs après l'ajout
        resetFields();
    }


    // Réinitialiser les champs du formulaire
    private void resetFields() {
        txtTitre.clear();
        txtContenu.clear();
        txtAuteur.clear();
        txtImage.clear();
        txtVideo.clear();
        datePublication.setValue(null);
        comboCategorie.getSelectionModel().clearSelection();
    }
}
