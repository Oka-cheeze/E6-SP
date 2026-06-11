package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import javax.swing.*;

import controleur.NeigeEtSoleil;

public class VueGeneral extends JFrame implements ActionListener {

    private JPanel  panelMenu      = new JPanel();
    private JButton btProfil       = new JButton("Profil");
    private JButton btUtilisateurs = new JButton("Utilisateurs");
    private JButton btHabitations  = new JButton("Habitations");
    private JButton btReservations = new JButton("Réservations");
    private JButton btStats        = new JButton("Stats");
    private JButton btQuitter      = new JButton("Quitter");

    private static PanelProfil       unPanelProfil       = new PanelProfil("Gestion du profil");
    private static PanelUsers        unPanelUsers        = new PanelUsers("Gestion des utilisateurs");
    private static PanelHabitations  unPanelHabitations  = new PanelHabitations("Gestion des habitations");
    private static PanelReservations unPanelReservations = new PanelReservations("Gestion des réservations");
    private static PanelStats        unPanelStats        = new PanelStats("Statistiques du parc");

    public VueGeneral() {
        this.setTitle("Neige et Soleil 2026");
        this.setBounds(10, 10, 1100, 600);
        this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        this.setResizable(false);
        this.setLayout(null);
        this.getContentPane().setBackground(new Color(28, 57, 94));

        // Menu horizontal en haut — identique à OrangeEvent
        this.panelMenu.setBounds(100, 10, 800, 40);
        this.panelMenu.setLayout(new GridLayout(1, 6));
        this.panelMenu.setBackground(Color.gray);

        this.panelMenu.add(this.btProfil);
        this.panelMenu.add(this.btUtilisateurs);
        this.panelMenu.add(this.btHabitations);
        this.panelMenu.add(this.btReservations);
        this.panelMenu.add(this.btStats);
        this.panelMenu.add(this.btQuitter);

        this.add(panelMenu);

        // Listeners
        this.btProfil.addActionListener(this);
        this.btUtilisateurs.addActionListener(this);
        this.btHabitations.addActionListener(this);
        this.btReservations.addActionListener(this);
        this.btStats.addActionListener(this);
        this.btQuitter.addActionListener(this);

        // Ajout des panels
        this.add(unPanelProfil);
        this.add(unPanelUsers);
        this.add(unPanelHabitations);
        this.add(unPanelReservations);
        this.add(unPanelStats);

        this.setVisible(true);
    }

    public void afficherPanel(int choix) {
        unPanelProfil.setVisible(false);
        unPanelUsers.setVisible(false);
        unPanelHabitations.setVisible(false);
        unPanelReservations.setVisible(false);
        unPanelStats.setVisible(false);

        switch (choix) {
            case 0: unPanelProfil.setVisible(true);       break;
            case 1: unPanelUsers.setVisible(true);        break;
            case 2: unPanelHabitations.setVisible(true);  break;
            case 3: unPanelReservations.setVisible(true); break;
            case 4: unPanelStats.setVisible(true);        break;
        }
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if      (e.getSource() == this.btProfil)       afficherPanel(0);
        else if (e.getSource() == this.btUtilisateurs) afficherPanel(1);
        else if (e.getSource() == this.btHabitations)  afficherPanel(2);
        else if (e.getSource() == this.btReservations) afficherPanel(3);
        else if (e.getSource() == this.btStats)        afficherPanel(4);
        else if (e.getSource() == this.btQuitter) {
            NeigeEtSoleil.rendreVisibleVueConnexion(true);
            NeigeEtSoleil.creerDetruireVueGeneral(false);
        }
    }
}
