package services;

import models.CategorieRessource;
import utils.MyDb;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class CategorieRessourceService {

    public List<CategorieRessource> getAllCategories() {
        List<CategorieRessource> categories = new ArrayList<>();
        String req = "SELECT * FROM categorie_ressource";

        try {
            Statement st = MyDb.getInstance().getConn().createStatement();
            ResultSet rs = st.executeQuery(req);

            while (rs.next()) {
                CategorieRessource cat = new CategorieRessource(
                        rs.getInt("id"),
                        rs.getString("nom")
                );
                categories.add(cat);
            }

        } catch (SQLException e) {
            System.out.println("Erreur lors du chargement des catégories : " + e.getMessage());
        }

        return categories;
    }
}
