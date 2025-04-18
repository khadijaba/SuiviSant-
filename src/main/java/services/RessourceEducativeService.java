package services;

import models.CategorieRessource;
import models.RessourceEducative;
import utils.MyDb;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class RessourceEducativeService {

    private final Connection connection;

    public RessourceEducativeService() {
        this.connection = MyDb.getInstance().getConn();
    }

    // ✅ Ajouter une ressource
    public void ajouterRessource(RessourceEducative ressource) {
        String req = "INSERT INTO ressource_educative (titre, contenu, date_publication, auteur, image, video, categorie_id) VALUES (?, ?, ?, ?, ?, ?, ?)";

        try (PreparedStatement pst = connection.prepareStatement(req)) {
            pst.setString(1, ressource.getTitre());
            pst.setString(2, ressource.getContenu());
            pst.setTimestamp(3, Timestamp.valueOf(ressource.getDatePublication()));
            pst.setString(4, ressource.getAuteur());
            pst.setString(5, ressource.getImage());
            pst.setString(6, ressource.getVideo());
            pst.setInt(7, ressource.getCategorie().getId());

            int rowsAffected = pst.executeUpdate();
            if (rowsAffected > 0) {
                System.out.println("✅ Ressource ajoutée avec succès : " + ressource.getTitre());
            } else {
                System.out.println("⚠️ Aucune ressource n'a été ajoutée.");
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de l'ajout : " + e.getMessage());
        }
    }

    // Dans RessourceEducativeService.java
    public List<CategorieRessource> getAllCategories() {
        List<CategorieRessource> categories = new ArrayList<>();
        String query = "SELECT * FROM categorie_ressource"; // A adapter à ton schéma

        try (Statement stmt = connection.createStatement(); ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) {
                CategorieRessource category = new CategorieRessource();
                category.setId(rs.getInt("id"));
                category.setNom(rs.getString("nom")); // Adapter cette ligne selon tes colonnes
                categories.add(category);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return categories;
    }

    // ✅ Modifier une ressource
    public boolean modifierRessource(RessourceEducative ressource) {
        String query = "UPDATE ressource_educative SET titre = ?, contenu = ?, auteur = ?, image = ?, video = ?, date_publication = ?, categorie_id = ? WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, ressource.getTitre());
            stmt.setString(2, ressource.getContenu());
            stmt.setString(3, ressource.getAuteur());
            stmt.setString(4, ressource.getImage());
            stmt.setString(5, ressource.getVideo());
            stmt.setTimestamp(6, Timestamp.valueOf(ressource.getDatePublication()));
            stmt.setInt(7, ressource.getCategorie().getId());
            stmt.setInt(8, ressource.getId());

            int rows = stmt.executeUpdate();
            return rows > 0;

        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    // ✅ Supprimer une ressource par ID
    public boolean supprimerRessource(int id) {
        String query = "DELETE FROM ressource_educative WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            int rows = stmt.executeUpdate();
            return rows > 0;

        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
    // ✅ Récupérer toutes les ressources éducatives avec leur catégorie
    public List<RessourceEducative> getAllRessources() {
        List<RessourceEducative> ressources = new ArrayList<>();
        String query = "SELECT r.*, c.nom AS nom_categorie FROM ressource_educative r " +
                "JOIN categorie_ressource c ON r.categorie_id = c.id";

        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {

            while (rs.next()) {
                RessourceEducative res = new RessourceEducative();
                res.setId(rs.getInt("id"));
                res.setTitre(rs.getString("titre"));
                res.setContenu(rs.getString("contenu"));
                res.setAuteur(rs.getString("auteur"));
                res.setImage(rs.getString("image"));
                res.setVideo(rs.getString("video"));

                Timestamp dateTimestamp = rs.getTimestamp("date_publication");
                if (dateTimestamp != null) {
                    res.setDatePublication(dateTimestamp.toLocalDateTime());
                }

                // Créer et associer la catégorie
                CategorieRessource categorie = new CategorieRessource();
                categorie.setId(rs.getInt("categorie_id"));
                categorie.setNom(rs.getString("nom_categorie"));
                res.setCategorie(categorie);

                ressources.add(res);
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }

        return ressources;
    }

}
