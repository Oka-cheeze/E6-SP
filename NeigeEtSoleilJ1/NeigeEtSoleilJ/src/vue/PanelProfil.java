package vue;

import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.*;
import javax.swing.*;

import controleur.Controleur;
import controleur.NeigeEtSoleil;
import controleur.User;

public class PanelProfil extends PanelPrincipal implements ActionListener {

    private static JTextArea txtInfo = new JTextArea();
    private static User      unAdmin;

    private JButton btModifier = new JButton("Modifier");
    private JButton btAnnuler  = new JButton("Annuler");
    private JButton btValider  = new JButton("Valider");

    private JPanel     panelForm = new JPanel();
    private JTextField txtNom    = new JTextField();
    private JTextField txtPrenom = new JTextField();
    private JTextField txtEmail  = new JTextField();
    private JTextField txtTel    = new JTextField();
    private JTextField txtVille  = new JTextField();

    public PanelProfil(String titre) {
        super(titre);

        // Zone info texte à gauche
        txtInfo.setBounds(80, 80, 300, 220);
        txtInfo.setBackground(Color.gray);
        txtInfo.setEditable(false);
        this.add(txtInfo);

        // Bouton modifier
        this.btModifier.setBounds(300, 310, 100, 40);
        this.add(btModifier);

        // Formulaire de modification (caché par défaut)
        this.panelForm.setBackground(Color.gray);
        this.panelForm.setBounds(500, 80, 400, 280);
        this.panelForm.setLayout(new GridLayout(7, 2, 10, 10));

        this.panelForm.add(new JLabel("Nom :"));
        this.panelForm.add(txtNom);

        this.panelForm.add(new JLabel("Prénom :"));
        this.panelForm.add(txtPrenom);

        this.panelForm.add(new JLabel("Email :"));
        this.panelForm.add(txtEmail);

        this.panelForm.add(new JLabel("Téléphone :"));
        this.panelForm.add(txtTel);

        this.panelForm.add(new JLabel("Ville :"));
        this.panelForm.add(txtVille);

        this.panelForm.add(this.btAnnuler);
        this.panelForm.add(this.btValider);

        this.panelForm.setVisible(false);
        this.add(panelForm);

        this.btModifier.addActionListener(this);
        this.btValider.addActionListener(this);
        this.btAnnuler.addActionListener(this);
    }

    public static void actualiserInfos() {
        unAdmin = NeigeEtSoleil.getAdminConnecte();
        if (unAdmin != null) {
            txtInfo.setText(
                "\n____________INFO PROFIL___________\n"
              + "\n\n Nom         : " + unAdmin.getNom()
              + "\n\n Prénom      : " + unAdmin.getPrenom()
              + "\n\n Email       : " + unAdmin.getEmail()
              + "\n\n Téléphone   : " + (unAdmin.getTel()   != null ? unAdmin.getTel()   : "-")
              + "\n\n Ville       : " + (unAdmin.getVille() != null ? unAdmin.getVille() : "-")
              + "\n\n Rôle        : " + unAdmin.getRole()
              + "\n__________________________________"
            );
        }
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == this.btModifier) {
            this.panelForm.setVisible(true);
            this.txtNom.setText(unAdmin.getNom());
            this.txtPrenom.setText(unAdmin.getPrenom());
            this.txtEmail.setText(unAdmin.getEmail());
            this.txtTel.setText(unAdmin.getTel() != null ? unAdmin.getTel() : "");
            this.txtVille.setText(unAdmin.getVille() != null ? unAdmin.getVille() : "");

        } else if (e.getSource() == this.btAnnuler) {
            this.panelForm.setVisible(false);

        } else if (e.getSource() == this.btValider) {
            unAdmin.setNom(txtNom.getText());
            unAdmin.setPrenom(txtPrenom.getText());
            unAdmin.setEmail(txtEmail.getText());
            unAdmin.setTel(txtTel.getText());
            unAdmin.setVille(txtVille.getText());

            Controleur.updateUser(unAdmin);
            actualiserInfos();
            panelForm.setVisible(false);
            JOptionPane.showMessageDialog(this, "Profil mis à jour !");
        }
    }
}
