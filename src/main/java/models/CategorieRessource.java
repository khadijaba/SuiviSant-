package models;

public class CategorieRessource {
    private int id;
    private String nom;

    public CategorieRessource() {
    }

    public CategorieRessource(int id, String nom) {
        this.id = id;
        this.nom = nom;
    }

    // Getters et setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }
    public CategorieRessource(String nom) {
        this.nom = nom;
    }
    @Override
    public String toString() {
        return nom;
    }
}