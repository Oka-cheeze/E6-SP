package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import java.util.ArrayList;
import javax.swing.*;
import javax.swing.event.*;

import controleur.Controleur;
import controleur.Tableau;
import controleur.User;

public class PanelUsers extends PanelPrincipal implements ActionListener {

    private JPanel     panelForm   = new JPanel();
    private JTextField txtNom      = new JTextField();
    private JTextField txtPrenom   = new JTextField();
    private JTextField txtEmail    = new JTextField();
    private JTextField txtTel      = new JTextField();
    private JTextField txtVille    = new JTextField();
    private JComboBox<String> cbxRole = new JComboBox<>();

    private JButton btAnnuler  = new JButton("Annuler");
    private JButton btValider  = new JButton("Valider");
    private JButton btModifier = new JButton("Modifier");
    private JButton btSupprimer= new JButton("Supprimer");

    private JPanel     panelFiltre = new JPanel();
    private JTextField txtFiltre   = new JTextField();
    private JButton    btFiltrer   = new JButton("Filtrer");

    private JTable     tableUsers;
    private JScrollPane scrollUsers;
    private Tableau    unTableau;
    private JLabel     lbNbUsers = new JLabel();

    public PanelUsers(String titre) {
        super(titre);

        cbxRole.addItem("client");
        cbxRole.addItem("proprietaire");
        cbxRole.addItem("admin");

        // Formulaire gauche — même style que OrangeEvent
        this.panelForm.setBackground(Color.gray);
        this.panelForm.setLayout(new GridLayout(8, 2, 10, 10));
        this.panelForm.setBounds(60, 120, 320, 320);

        this.panelForm.add(new JLabel("Nom :"));
        this.panelForm.add(this.txtNom);

        this.panelForm.add(new JLabel("Prénom :"));
        this.panelForm.add(this.txtPrenom);

        this.panelForm.add(new JLabel("Email :"));
        this.panelForm.add(this.txtEmail);

        this.panelForm.add(new JLabel("Téléphone :"));
        this.panelForm.add(this.txtTel);

        this.panelForm.add(new JLabel("Ville :"));
        this.panelForm.add(this.txtVille);

        this.panelForm.add(new JLabel("Rôle :"));
        this.panelForm.add(this.cbxRole);

        this.panelForm.add(this.btAnnuler);
        this.panelForm.add(this.btValider);

        this.panelForm.add(this.btModifier);
        this.panelForm.add(this.btSupprimer);

        this.add(this.panelForm);

        this.btModifier.setEnabled(false);
        this.btSupprimer.setEnabled(false);

        // Filtre — même style que OrangeEvent
        this.panelFiltre.setBackground(Color.gray);
        this.panelFiltre.setLayout(new GridLayout(1, 3, 20, 20));
        this.panelFiltre.setBounds(500, 80, 400, 30);

        this.panelFiltre.add(new JLabel("Filtrer par :"));
        this.panelFiltre.add(this.txtFiltre);
        this.panelFiltre.add(this.btFiltrer);
        this.add(this.panelFiltre);

        // Listeners
        this.btAnnuler.addActionListener(this);
        this.btValider.addActionListener(this);
        this.btFiltrer.addActionListener(this);
        this.btModifier.addActionListener(this);
        this.btSupprimer.addActionListener(this);

        // JTable droite
        String[] entetes = {"ID", "Nom", "Prénom", "Email", "Rôle", "Ville", "Tél."};
        this.unTableau  = new Tableau(this.obtenirDonnees(""), entetes);
        this.tableUsers = new JTable(this.unTableau);
        this.scrollUsers = new JScrollPane(this.tableUsers);
        this.scrollUsers.setBounds(450, 120, 580, 300);
        this.add(this.scrollUsers);

        // Clic sur une ligne → remplit le formulaire
        this.tableUsers.addMouseListener(new java.awt.event.MouseAdapter() {
            @Override
            public void mouseClicked(java.awt.event.MouseEvent e) {
                int row = tableUsers.getSelectedRow();
                txtNom.setText(unTableau.getValueAt(row, 1).toString());
                txtPrenom.setText(unTableau.getValueAt(row, 2).toString());
                txtEmail.setText(unTableau.getValueAt(row, 3).toString());
                cbxRole.setSelectedItem(unTableau.getValueAt(row, 4).toString());
                txtVille.setText(unTableau.getValueAt(row, 5).toString());
                txtTel.setText(unTableau.getValueAt(row, 6) != null
                        ? unTableau.getValueAt(row, 6).toString() : "");
                btModifier.setEnabled(true);
                btSupprimer.setEnabled(true);
            }
        });

        this.lbNbUsers.setText("Nombre d'utilisateurs : " + unTableau.getRowCount());
        this.lbNbUsers.setBounds(600, 440, 400, 20);
        this.add(this.lbNbUsers);
    }

    private Object[][] obtenirDonnees(String filtre) {
        ArrayList<User> lesUsers = Controleur.selectAllUsers(filtre);
        Object[][] mat = new Object[lesUsers.size()][7];
        for (int i = 0; i < lesUsers.size(); i++) {
            User u = lesUsers.get(i);
            mat[i][0] = u.getIdUser();
            mat[i][1] = u.getNom();
            mat[i][2] = u.getPrenom();
            mat[i][3] = u.getEmail();
            mat[i][4] = u.getRole();
            mat[i][5] = u.getVille();
            mat[i][6] = u.getTel();
        }
        return mat;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btAnnuler) {
            viderChamps();

        } else if (e.getSource() == this.btValider) {
            insertUser();

        } else if (e.getSource() == this.btFiltrer) {
            this.unTableau.setDonnees(this.obtenirDonnees(this.txtFiltre.getText()));
            this.lbNbUsers.setText("Nombre d'utilisateurs : " + unTableau.getRowCount());

        } else if (e.getSource() == this.btModifier) {
            updateUser();

        } else if (e.getSource() == this.btSupprimer) {
            deleteUser();
        }
    }

    public void insertUser() {
        String nom    = txtNom.getText();
        String prenom = txtPrenom.getText();
        String email  = txtEmail.getText();
        if (nom.equals("") || prenom.equals("") || email.equals("")) {
            JOptionPane.showMessageDialog(this, "Nom, prénom et email obligatoires.");
            return;
        }
        User u = new User(nom, prenom, email,
                cbxRole.getSelectedItem().toString(),
                txtTel.getText(), "", "", txtVille.getText());
        Controleur.insertUser(u);
        JOptionPane.showMessageDialog(this, "Utilisateur ajouté ! MDP par défaut : changeme");
        this.unTableau.setDonnees(this.obtenirDonnees(""));
        this.lbNbUsers.setText("Nombre d'utilisateurs : " + unTableau.getRowCount());
        viderChamps();
    }

    public void updateUser() {
        int row    = tableUsers.getSelectedRow();
        int idUser = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
        User u = new User(idUser,
                txtNom.getText(), txtPrenom.getText(), txtEmail.getText(),
                cbxRole.getSelectedItem().toString(),
                txtTel.getText(), "", "", txtVille.getText());
        Controleur.updateUser(u);
        JOptionPane.showMessageDialog(this, "Utilisateur modifié avec succès.");
        this.unTableau.setDonnees(this.obtenirDonnees(""));
        viderChamps();
    }

    public void deleteUser() {
        int row    = tableUsers.getSelectedRow();
        int idUser = Integer.parseInt(unTableau.getValueAt(row, 0).toString());
        if (JOptionPane.showConfirmDialog(this, "Supprimer cet utilisateur ?",
                "Suppression", JOptionPane.YES_NO_OPTION) == 0) {
            Controleur.deleteUser(idUser);
            JOptionPane.showMessageDialog(this, "Utilisateur supprimé.");
            this.unTableau.setDonnees(this.obtenirDonnees(""));
            this.lbNbUsers.setText("Nombre d'utilisateurs : " + unTableau.getRowCount());
            viderChamps();
        }
    }

    public void viderChamps() {
        txtNom.setText(""); txtPrenom.setText(""); txtEmail.setText("");
        txtTel.setText(""); txtVille.setText("");
        cbxRole.setSelectedIndex(0);
        btModifier.setEnabled(false);
        btSupprimer.setEnabled(false);
        tableUsers.clearSelection();
    }
}
