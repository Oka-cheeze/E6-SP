package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import java.util.ArrayList;
import javax.swing.*;

import controleur.Controleur;
import controleur.Habitation;
import controleur.Tableau;

public class PanelHabitations extends PanelPrincipal implements ActionListener {

    private JPanel     panelForm    = new JPanel();
    private JTextField txtTitre     = new JTextField();
    private JTextField txtVille     = new JTextField();
    private JTextField txtStation   = new JTextField();
    private JTextField txtPrix      = new JTextField();
    private JTextField txtSurface   = new JTextField();
    private JComboBox<String> cbxStatut = new JComboBox<>();

    private JButton btValiderContrat = new JButton("Valider contrat");
    private JButton btRejeterContrat = new JButton("Rejeter contrat");
    private JButton btSupprimer      = new JButton("Supprimer");
    private JButton btAnnuler        = new JButton("Annuler");

    private JPanel     panelFiltre = new JPanel();
    private JTextField txtFiltre   = new JTextField();
    private JButton    btFiltrer   = new JButton("Filtrer");

    private JTable      tableHabit;
    private JScrollPane scrollHabit;
    private Tableau     unTableau;
    private JLabel      lbNb = new JLabel();

    public PanelHabitations(String titre) {
        super(titre);

        cbxStatut.addItem("en_attente");
        cbxStatut.addItem("disponible");
        cbxStatut.addItem("occupe");
        cbxStatut.addItem("rejete");
        cbxStatut.addItem("maintenance");

        // Formulaire gauche
        this.panelForm.setBackground(Color.gray);
        this.panelForm.setLayout(new GridLayout(8, 2, 10, 10));
        this.panelForm.setBounds(60, 120, 320, 300);

        this.panelForm.add(new JLabel("Titre :"));
        this.panelForm.add(this.txtTitre);

        this.panelForm.add(new JLabel("Ville :"));
        this.panelForm.add(this.txtVille);

        this.panelForm.add(new JLabel("Station :"));
        this.panelForm.add(this.txtStation);

        this.panelForm.add(new JLabel("Prix / nuit :"));
        this.panelForm.add(this.txtPrix);

        this.panelForm.add(new JLabel("Surface (m²) :"));
        this.panelForm.add(this.txtSurface);

        this.panelForm.add(new JLabel("Statut :"));
        this.panelForm.add(this.cbxStatut);

        this.panelForm.add(this.btAnnuler);
        this.panelForm.add(new JLabel(""));

        this.panelForm.add(this.btValiderContrat);
        this.panelForm.add(this.btRejeterContrat);

        this.add(this.panelForm);

        // Bouton supprimer séparé en dessous
        this.btSupprimer.setBounds(60, 435, 150, 30);
        this.add(this.btSupprimer);

        this.btValiderContrat.setEnabled(false);
        this.btRejeterContrat.setEnabled(false);
        this.btSupprimer.setEnabled(false);

        // Filtre
        this.panelFiltre.setBackground(Color.gray);
        this.panelFiltre.setLayout(new GridLayout(1, 3, 20, 20));
        this.panelFiltre.setBounds(500, 80, 400, 30);

        this.panelFiltre.add(new JLabel("Filtrer par :"));
        this.panelFiltre.add(this.txtFiltre);
        this.panelFiltre.add(this.btFiltrer);
        this.add(this.panelFiltre);

        // Listeners
        this.btAnnuler.addActionListener(this);
        this.btFiltrer.addActionListener(this);
        this.btValiderContrat.addActionListener(this);
        this.btRejeterContrat.addActionListener(this);
        this.btSupprimer.addActionListener(this);

        // JTable
        String[] entetes = {"ID", "Titre", "Type", "Station", "Ville", "Prix/nuit", "Statut"};
        this.unTableau  = new Tableau(this.obtenirDonnees(""), entetes);
        this.tableHabit = new JTable(this.unTableau);
        this.scrollHabit = new JScrollPane(this.tableHabit);
        this.scrollHabit.setBounds(450, 120, 580, 300);
        this.add(this.scrollHabit);

        this.tableHabit.addMouseListener(new java.awt.event.MouseAdapter() {
            @Override
            public void mouseClicked(java.awt.event.MouseEvent e) {
                int row = tableHabit.getSelectedRow();
                txtTitre.setText(unTableau.getValueAt(row, 1).toString());
                txtStation.setText(unTableau.getValueAt(row, 3).toString());
                txtVille.setText(unTableau.getValueAt(row, 4).toString());
                txtPrix.setText(unTableau.getValueAt(row, 5).toString());
                txtSurface.setText("-");
                String statut = unTableau.getValueAt(row, 6).toString();
                cbxStatut.setSelectedItem(statut);

                btValiderContrat.setEnabled(statut.equals("en_attente"));
                btRejeterContrat.setEnabled(statut.equals("en_attente"));
                btSupprimer.setEnabled(true);
            }
        });

        this.lbNb.setText("Nombre de logements : " + unTableau.getRowCount());
        this.lbNb.setBounds(600, 440, 400, 20);
        this.add(this.lbNb);
    }

    private Object[][] obtenirDonnees(String filtre) {
        ArrayList<Habitation> liste = Controleur.selectAllHabitations(filtre);
        Object[][] mat = new Object[liste.size()][7];
        for (int i = 0; i < liste.size(); i++) {
            Habitation h = liste.get(i);
            mat[i][0] = h.getIdHabit();
            mat[i][1] = h.getTitre();
            mat[i][2] = h.getType();
            mat[i][3] = h.getStation();
            mat[i][4] = h.getVille();
            mat[i][5] = h.getPrixNuit();
            mat[i][6] = h.getStatut();
        }
        return mat;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btAnnuler) {
            viderChamps();

        } else if (e.getSource() == this.btFiltrer) {
            this.unTableau.setDonnees(this.obtenirDonnees(this.txtFiltre.getText()));
            this.lbNb.setText("Nombre de logements : " + unTableau.getRowCount());

        } else if (e.getSource() == this.btValiderContrat) {
            int row     = tableHabit.getSelectedRow();
            int idHabit = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            if (JOptionPane.showConfirmDialog(this, "Valider ce logement ?",
                    "Validation", JOptionPane.YES_NO_OPTION) == 0) {
                Controleur.validerHabitation(idHabit);
                JOptionPane.showMessageDialog(this, "Logement validé !");
                this.unTableau.setDonnees(this.obtenirDonnees(""));
                this.lbNb.setText("Nombre de logements : " + unTableau.getRowCount());
                viderChamps();
            }

        } else if (e.getSource() == this.btRejeterContrat) {
            int row     = tableHabit.getSelectedRow();
            int idHabit = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            String motif = JOptionPane.showInputDialog(this, "Motif de refus :");
            if (motif != null && !motif.trim().isEmpty()) {
                Controleur.rejeterHabitation(idHabit, motif);
                JOptionPane.showMessageDialog(this, "Logement rejeté.");
                this.unTableau.setDonnees(this.obtenirDonnees(""));
                this.lbNb.setText("Nombre de logements : " + unTableau.getRowCount());
                viderChamps();
            }

        } else if (e.getSource() == this.btSupprimer) {
            int row     = tableHabit.getSelectedRow();
            int idHabit = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            if (JOptionPane.showConfirmDialog(this, "Supprimer ce logement ?",
                    "Suppression", JOptionPane.YES_NO_OPTION) == 0) {
                Controleur.deleteHabitation(idHabit);
                JOptionPane.showMessageDialog(this, "Logement supprimé.");
                this.unTableau.setDonnees(this.obtenirDonnees(""));
                this.lbNb.setText("Nombre de logements : " + unTableau.getRowCount());
                viderChamps();
            }
        }
    }

    public void viderChamps() {
        txtTitre.setText(""); txtVille.setText(""); txtStation.setText("");
        txtPrix.setText(""); txtSurface.setText("");
        cbxStatut.setSelectedIndex(0);
        btValiderContrat.setEnabled(false);
        btRejeterContrat.setEnabled(false);
        btSupprimer.setEnabled(false);
        tableHabit.clearSelection();
    }
}
