package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.stage.Stage;
import models.RessourceEducative;
import models.CategorieRessource;
import services.RessourceEducativeService;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.List;

public class modifierRController {

    @FXML
    private TextField titreField;

    @FXML
    private TextArea contenuArea;

    @FXML
    private TextField auteurField;

    @FXML
    private TextField imageField;

    @FXML
    private TextField videoField;

    @FXML
    private DatePicker datePublicationPicker;

    @FXML
    private ComboBox<CategorieRessource> categorieComboBox;

    private RessourceEducative ressource;
    private final RessourceEducativeService service = new RessourceEducativeService();

    public void setRessource(RessourceEducative ressource) {
        this.ressource = ressource;

        // Pré-remplir les champs
        titreField.setText(ressource.getTitre());
        contenuArea.setText(ressource.getContenu());
        auteurField.setText(ressource.getAuteur());
        imageField.setText(ressource.getImage());
        videoField.setText(ressource.getVideo());

        // Conversion de LocalDateTime -> LocalDate
        if (ressource.getDatePublication() != null) {
            datePublicationPicker.setValue(ressource.getDatePublication().toLocalDate());
        }

        // Charger les catégories (depuis la base ou manuellement)
        List<CategorieRessource> categories = service.getAllCategories(); // à adapter à ton service
        categorieComboBox.getItems().addAll(categories);

        // Sélectionner la catégorie actuelle
        categorieComboBox.setValue(ressource.getCategorie());
    }



    @FXML
    private void modifierRessource() {
        if (ressource == null) return;

        // Mettre à jour les champs
        ressource.setTitre(titreField.getText());
        ressource.setContenu(contenuArea.getText());
        ressource.setAuteur(auteurField.getText());
        ressource.setImage(imageField.getText());
        ressource.setVideo(videoField.getText());

        // Conversion LocalDate -> LocalDateTime
        LocalDate selectedDate = datePublicationPicker.getValue();
        if (selectedDate != null) {
            ressource.setDatePublication(selectedDate.atStartOfDay());
        }

        ressource.setCategorie(categorieComboBox.getValue());

        // Mise à jour via service
        service.modifierRessource(ressource);

        // Fermer la fenêtre
        Stage stage = (Stage) titreField.getScene().getWindow();
        stage.close();
    }
}
