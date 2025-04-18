package models;

import java.time.LocalDateTime;

public class RessourceEducative {
    private int id;
    private String titre;
    private String contenu;
    private String auteur;
    private String image;
    private String video;
    private LocalDateTime datePublication;
    private CategorieRessource categorie;  // Lien avec CategorieRessource

    public RessourceEducative(String titre, String contenu, String auteur, String image, String video, LocalDateTime datePublication, CategorieRessource categorie) {
        this.titre = titre;
        this.contenu = contenu;
        this.auteur = auteur;
        this.image = image;
        this.video = video;
        this.datePublication = datePublication;
        this.categorie = categorie;
        this.id = 0;
    }

    public RessourceEducative() {
    }
    public String getTitre() {
        return titre;
    }

    public void setTitre(String titre) {
        this.titre = titre;
    }

    public String getContenu() {
        return contenu;
    }

    public void setContenu(String contenu) {
        this.contenu = contenu;
    }

    public String getAuteur() {
        return auteur;
    }

    public void setAuteur(String auteur) {
        this.auteur = auteur;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getVideo() {
        return video;
    }

    public void setVideo(String video) {
        this.video = video;
    }

    public LocalDateTime getDatePublication() {
        return datePublication;
    }

    public void setDatePublication(LocalDateTime datePublication) {
        this.datePublication = datePublication;
    }

    public CategorieRessource getCategorie() {
        return categorie;
    }

    public void setCategorie(CategorieRessource categorie) {
        this.categorie = categorie;
    }
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

}
