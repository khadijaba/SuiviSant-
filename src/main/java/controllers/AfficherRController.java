package controllers;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import models.RessourceEducative;
import services.RessourceEducativeService;

import java.io.IOException;
import java.util.List;
import java.util.Optional;

public class AfficherRController {

    @FXML
    private TableView<RessourceEducative> tableRessources;

    @FXML
    private TableColumn<RessourceEducative, Integer> colId;

    @FXML
    private TableColumn<RessourceEducative, String> colTitre;

    @FXML
    private TableColumn<RessourceEducative, String> colContenu;

    @FXML
    private TableColumn<RessourceEducative, String> colAuteur;

    @FXML
    private TableColumn<RessourceEducative, String> colDate;

    @FXML
    private TableColumn<RessourceEducative, String> colCategorie;

    @FXML
    private TableColumn<RessourceEducative, Void> colModifier;

    @FXML
    private TableColumn<RessourceEducative, Void> colSupprimer;

    private final RessourceEducativeService service = new RessourceEducativeService();

    @FXML
    public void initialize() {
        colId.setCellValueFactory(new PropertyValueFactory<>("id"));
        colTitre.setCellValueFactory(new PropertyValueFactory<>("titre"));
        colContenu.setCellValueFactory(new PropertyValueFactory<>("contenu"));
        colAuteur.setCellValueFactory(new PropertyValueFactory<>("auteur"));
        colDate.setCellValueFactory(new PropertyValueFactory<>("datePublication"));
        colCategorie.setCellValueFactory(new PropertyValueFactory<>("categorie"));

        ajouterBoutonsModifier();
        ajouterBoutonsSupprimer();

        chargerRessources();
    }

    private void chargerRessources() {
        List<RessourceEducative> ressources = service.getAllRessources();
        tableRessources.getItems().setAll(ressources);
    }

    private void ajouterBoutonsModifier() {
        colModifier.setCellFactory(col -> new TableCell<>() {
            private final Button btn = new Button("Modifier");

            {
                btn.setOnAction(e -> {
                    RessourceEducative res = getTableView().getItems().get(getIndex());
                    ouvrirFenetreModification(res);
                });
            }

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                setGraphic(empty ? null : btn);
            }
        });
    }

    private void ajouterBoutonsSupprimer() {
        colSupprimer.setCellFactory(col -> new TableCell<>() {
            private final Button btn = new Button("Supprimer");

            {
                btn.setOnAction(e -> {
                    RessourceEducative res = getTableView().getItems().get(getIndex());
                    Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
                    alert.setTitle("Confirmation");
                    alert.setHeaderText("Êtes-vous sûr de vouloir supprimer cette ressource ?");
                    alert.setContentText("Cette action est irréversible.");
                    Optional<ButtonType> result = alert.showAndWait();

                    if (result.isPresent() && result.get() == ButtonType.OK) {
                        service.supprimerRessource(res.getId());
                        chargerRessources();
                    }
                });
            }

            @Override
            protected void updateItem(Void item, boolean empty) {
                super.updateItem(item, empty);
                setGraphic(empty ? null : btn);
            }
        });
    }

    @FXML
    private void ouvrirAjout() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/AjouterR.fxml"));
            Parent root = loader.load();

            Stage stage = new Stage();
            stage.setScene(new Scene(root));
            stage.setTitle("Ajouter une ressource");
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void ouvrirFenetreModification(RessourceEducative ressource) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/ModifierR.fxml"));
            Parent root = loader.load();

            // Passer la ressource au contrôleur
            modifierRController controller = loader.getController();
            controller.setRessource(ressource);

            Stage stage = new Stage();
            stage.setTitle("Modifier Ressource");
            stage.setScene(new Scene(root));
            stage.show();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }



}
