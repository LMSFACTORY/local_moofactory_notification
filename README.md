# **local_moofactory_notification**  
Gestion de notifications via des tâches programmées.

### **Fonctionnalités principales**  
- **siteevents_notification**  
- **coursesevents_notification**  
- **coursesenroll_notification**  
- **coursesaccess_notification**  
- **modulesaccess_notification**  

---

## **modulesaccess_notification**  
Cette fonctionnalité gère les notifications envoyées après la levée de restrictions d’accès à une activité.

### **Cas d’usage**  
1. À l’issue des cours, une enquête à chaud est mise à disposition des étudiants.  
2. Les étudiants ne peuvent voir l’enquête qu’après la levée des restrictions.  
3. Une notification est émise lorsque la ou les restrictions sont levées.  
4. Des relances peuvent être envoyées si l’étudiant n’a pas achevé l’enquête.

#### **Fréquence d’exécution**  
- La tâche programée s’exécute par défaut toutes les **2 minutes**.

#### **Conditions d’envoi des notifications**  
1. **Les notifications sont activées sur la plateforme**.  
2. **Les notifications pour la "levée de restrictions" sont activées** sur la plateforme.

Si ces deux conditions sont remplies :  
  1. On vérifie toutes les activités ayant des restrictions d’accès.  
  2. Parmi ces activités, on vérifie si **les notifications de levée de restrictions** sont activées pour chacune.  
  3. Si elles sont activées, les étapes suivantes sont réalisées pour **chaque participant inscrit au cours** :  
     - Vérification des permissions de l’utilisateur pour recevoir les notifications.  
     - Vérification si l’utilisateur est bloqué par une restriction pour accéder à l’activité.  
  4. Si toutes les conditions sont satisfaites, **une trace est stockée en base de données** :  
     - **TRACE 1** (situation initiale).  

#### **Comparaison à la prochaine exécution du CRON**  
- Lors de l’échéance suivante :  
  - La nouvelle situation (**TRACE 2**) est comparée à **TRACE 1**.  
  - Si l’utilisateur **reste bloqué** par la restriction :  
    - **Pas d’envoi de notification.**  
  - Si l’utilisateur **n’est plus bloqué** :  
    - Vérification s’il a **achevé l’activité** :  
      - **Oui** → Pas d’envoi de notification.  
      - **Non** → Notification envoyée.  

### **A noter**  

/!\ Pour que la tâche CRON soit opérationnelle, il faut lui laisser le temps de s’exécuter de façon à ce que le système puisse comparer la TRACE 1 à la TRACE 2. 
 
Lorsqu’une restriction est ajoutée à une activité, il faut laisser le temps à la tâche CRON de s’exécuter afin de vérifier une première fois si les utilisateurs ont accès à l’activité (TRACE 1). 

Si la restriction est levée immédiatement après avoir été ajoutée, la tâche CRON n’a pas le temps de s’exécuter à nouveau (TRACE 2). La levée de restriction n’est donc pas enregistrée dans la tâche et la notification ne peut pas être envoyée. 

C’est un élément à prendre en compte lors des phases de test. 

---
