package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import java.util.ArrayList;
import javax.swing.*;

import controleur.Controleur;
import controleur.Reservation;
import controleur.Tableau;

public class PanelReservations extends PanelPrincipal implements ActionListener {

    private JPanel     panelForm   = new JPanel();
    private JTextField txtClient   = new JTextField();
    private JTextField txtLogement = new JTextField();
    private JTextField txtDebut    = new JTextField();
    private JTextField txtFin      = new JTextField();
    private JTextField txtPersonnes= new JTextField();
    private JTextField txtTotal    = new JTextField();
    private JComboBox<String> cbxStatut = new JComboBox<>();

    private JButton btConfirmer = new JButton("Confirmer");
    private JButton btAnnuler   = new JButton("Annuler rés.");
    private JButton btSupprimer = new JButton("Supprimer");
    private JButton btEffacer   = new JButton("Effacer");

    private JPanel     panelFiltre = new JPanel();
    private JTextField txtFiltre   = new JTextField();
    private JButton    btFiltrer   = new JButton("Filtrer");

    private JTable      tableRes;
    private JScrollPane scrollRes;
    private Tableau     unTableau;
    private JLabel      lbNb = new JLabel();

    public PanelReservations(String titre) {
        super(titre);

        cbxStatut.addItem("en_attente");
        cbxStatut.addItem("confirmee");
        cbxStatut.addItem("annulee");

        // Formulaire gauche
        this.panelForm.setBackground(Color.gray);
        this.panelForm.setLayout(new GridLayout(9, 2, 10, 10));
        this.panelForm.setBounds(60, 120, 320, 340);

        this.panelForm.add(new JLabel("Client :"));
        this.panelForm.add(this.txtClient);

        this.panelForm.add(new JLabel("Logement :"));
        this.panelForm.add(this.txtLogement);

        this.panelForm.add(new JLabel("Arrivée :"));
        this.panelForm.add(this.txtDebut);

        this.panelForm.add(new JLabel("Départ :"));
        this.panelForm.add(this.txtFin);

        this.panelForm.add(new JLabel("Personnes :"));
        this.panelForm.add(this.txtPersonnes);

        this.panelForm.add(new JLabel("Total (€) :"));
        this.panelForm.add(this.txtTotal);

        this.panelForm.add(new JLabel("Statut :"));
        this.panelForm.add(this.cbxStatut);

        this.panelForm.add(this.btEffacer);
        this.panelForm.add(this.btConfirmer);

        this.panelForm.add(this.btAnnuler);
        this.panelForm.add(this.btSupprimer);

        this.add(this.panelForm);

        this.btConfirmer.setEnabled(false);
        this.btAnnuler.setEnabled(false);
        this.btSupprimer.setEnabled(false);

        // Filtre
        this.panelFiltre.setBackground(Color.gray);
        this.panelFiltre.setLayout(new GridLayout(1, 3, 20, 20));
        this.panelFiltre.setBounds(500, 80, 400, 30);

        this.panelFiltre.add(new JLabel("Filtrer par :"));
        this.panelFiltre.add(this.txtFiltre);
        this.panelFiltre.add(this.btFiltrer);
        this.add(this.panelFiltre);

        // Champs en lecture seule
        txtClient.setEditable(false);
        txtLogement.setEditable(false);
        txtDebut.setEditable(false);
        txtFin.setEditable(false);
        txtPersonnes.setEditable(false);
        txtTotal.setEditable(false);

        // Listeners
        this.btEffacer.addActionListener(this);
        this.btConfirmer.addActionListener(this);
        this.btAnnuler.addActionListener(this);
        this.btSupprimer.addActionListener(this);
        this.btFiltrer.addActionListener(this);

        // JTable
        String[] entetes = {"ID", "Client", "Logement", "Arrivée", "Départ", "Pers.", "Total €", "Statut"};
        this.unTableau = new Tableau(this.obtenirDonnees(""), entetes);
        this.tableRes  = new JTable(this.unTableau);
        this.scrollRes = new JScrollPane(this.tableRes);
        this.scrollRes.setBounds(450, 120, 580, 300);
        this.add(this.scrollRes);

        this.tableRes.addMouseListener(new java.awt.event.MouseAdapter() {
            @Override
            public void mouseClicked(java.awt.event.MouseEvent e) {
                int row = tableRes.getSelectedRow();
                txtClient.setText(unTableau.getValueAt(row, 1).toString());
                txtLogement.setText(unTableau.getValueAt(row, 2).toString());
                txtDebut.setText(unTableau.getValueAt(row, 3).toString());
                txtFin.setText(unTableau.getValueAt(row, 4).toString());
                txtPersonnes.setText(unTableau.getValueAt(row, 5).toString());
                txtTotal.setText(unTableau.getValueAt(row, 6).toString());
                String statut = unTableau.getValueAt(row, 7).toString();
                cbxStatut.setSelectedItem(statut);

                btConfirmer.setEnabled(!statut.equals("confirmee"));
                btAnnuler.setEnabled(!statut.equals("annulee"));
                btSupprimer.setEnabled(true);
            }
        });

        this.lbNb.setText("Nombre de réservations : " + unTableau.getRowCount());
        this.lbNb.setBounds(600, 440, 400, 20);
        this.add(this.lbNb);
    }

    private Object[][] obtenirDonnees(String filtre) {
        ArrayList<Reservation> liste = Controleur.selectAllReservations(filtre);
        Object[][] mat = new Object[liste.size()][8];
        for (int i = 0; i < liste.size(); i++) {
            Reservation r = liste.get(i);
            mat[i][0] = r.getIdRes();
            mat[i][1] = r.getNomClient();
            mat[i][2] = r.getTitreHabit();
            mat[i][3] = r.getDateDebut();
            mat[i][4] = r.getDateFin();
            mat[i][5] = r.getNbPersonnes();
            mat[i][6] = r.getPrixTotal();
            mat[i][7] = r.getStatut();
        }
        return mat;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btEffacer) {
            viderChamps();

        } else if (e.getSource() == this.btFiltrer) {
            this.unTableau.setDonnees(this.obtenirDonnees(this.txtFiltre.getText()));
            this.lbNb.setText("Nombre de réservations : " + unTableau.getRowCount());

        } else if (e.getSource() == this.btConfirmer) {
            int row   = tableRes.getSelectedRow();
            int idRes = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            Controleur.updateStatutReservation(idRes, "confirmee");
            JOptionPane.showMessageDialog(this, "Réservation confirmée !");
            this.unTableau.setDonnees(this.obtenirDonnees(""));
            viderChamps();

        } else if (e.getSource() == this.btAnnuler) {
            int row   = tableRes.getSelectedRow();
            int idRes = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            if (JOptionPane.showConfirmDialog(this, "Annuler cette réservation ?",
                    "Annulation", JOptionPane.YES_NO_OPTION) == 0) {
                Controleur.updateStatutReservation(idRes, "annulee");
                JOptionPane.showMessageDialog(this, "Réservation annulée.");
                this.unTableau.setDonnees(this.obtenirDonnees(""));
                viderChamps();
            }

        } else if (e.getSource() == this.btSupprimer) {
            int row   = tableRes.getSelectedRow();
            int idRes = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
            if (JOptionPane.showConfirmDialog(this, "Supprimer cette réservation ?",
                    "Suppression", JOptionPane.YES_NO_OPTION) == 0) {
                Controleur.deleteReservation(idRes);
                JOptionPane.showMessageDialog(this, "Réservation supprimée.");
                this.unTableau.setDonnees(this.obtenirDonnees(""));
                this.lbNb.setText("Nombre de réservations : " + unTableau.getRowCount());
                viderChamps();
            }
        }
    }

    public void viderChamps() {
        txtClient.setText(""); txtLogement.setText(""); txtDebut.setText("");
        txtFin.setText(""); txtPersonnes.setText(""); txtTotal.setText("");
        cbxStatut.setSelectedIndex(0);
        btConfirmer.setEnabled(false);
        btAnnuler.setEnabled(false);
        btSupprimer.setEnabled(false);
        tableRes.clearSelection();
    }
}
