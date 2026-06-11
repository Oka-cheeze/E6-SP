package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import javax.swing.*;

import controleur.Controleur;
import controleur.Tableau;

public class PanelStats extends PanelPrincipal implements ActionListener {

    private JButton btParStation = new JButton("Stats par Station");
    private JButton btParType    = new JButton("Stats par Type");

    private Tableau    unTableau;
    private JTable     tableStats;
    private JScrollPane scrollStats;
    private JLabel     lbSousTitre = new JLabel("Statistiques par station");

    public PanelStats(String titre) {
        super(titre);

        // Boutons de bascule
        JPanel panelBoutons = new JPanel();
        panelBoutons.setBackground(Color.gray);
        panelBoutons.setLayout(new GridLayout(1, 2, 20, 0));
        panelBoutons.setBounds(300, 80, 400, 35);
        panelBoutons.add(this.btParStation);
        panelBoutons.add(this.btParType);
        this.add(panelBoutons);

        // Sous-titre
        lbSousTitre.setBounds(80, 125, 400, 20);
        this.add(lbSousTitre);

        // Tableau stats
        String[] entetes = {"Station", "Prix min", "Prix max", "Prix moyen", "Nb logements"};
        this.unTableau  = new Tableau(Controleur.getStatsParStation(), entetes);
        this.tableStats = new JTable(this.unTableau);
        this.scrollStats = new JScrollPane(this.tableStats);
        this.scrollStats.setBounds(80, 150, 880, 280);
        this.add(this.scrollStats);

        this.btParStation.addActionListener(this);
        this.btParType.addActionListener(this);
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btParStation) {
            this.unTableau.setDonnees(Controleur.getStatsParStation());
            lbSousTitre.setText("Statistiques par station");

        } else if (e.getSource() == this.btParType) {
            this.unTableau.setDonnees(Controleur.getStatsParType());
            lbSousTitre.setText("Statistiques par type de logement");
        }
    }
}
