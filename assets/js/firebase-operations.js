import { db, auth, storage } from './firebase-config.js';

// Funções para Projetos
export const projectOperations = {
    // Adicionar novo projeto
    async addProject(projectData) {
        try {
            const docRef = await db.collection('projects').add({
                ...projectData,
                createdAt: firebase.firestore.FieldValue.serverTimestamp()
            });
            return { success: true, id: docRef.id };
        } catch (error) {
            console.error('Erro ao adicionar projeto:', error);
            return { success: false, error };
        }
    },

    // Buscar todos os projetos
    async getAllProjects() {
        try {
            const snapshot = await db.collection('projects')
                .orderBy('createdAt', 'desc')
                .get();
            
            return snapshot.docs.map(doc => ({
                id: doc.id,
                ...doc.data()
            }));
        } catch (error) {
            console.error('Erro ao buscar projetos:', error);
            return [];
        }
    },

    // Atualizar projeto
    async updateProject(projectId, projectData) {
        try {
            await db.collection('projects').doc(projectId).update(projectData);
            return { success: true };
        } catch (error) {
            console.error('Erro ao atualizar projeto:', error);
            return { success: false, error };
        }
    },

    // Deletar projeto
    async deleteProject(projectId) {
        try {
            await db.collection('projects').doc(projectId).delete();
            return { success: true };
        } catch (error) {
            console.error('Erro ao deletar projeto:', error);
            return { success: false, error };
        }
    }
};

// Funções para Contatos
export const contactOperations = {
    // Salvar mensagem de contato
    async saveContactMessage(messageData) {
        try {
            const docRef = await db.collection('contacts').add({
                ...messageData,
                createdAt: firebase.firestore.FieldValue.serverTimestamp()
            });
            return { success: true, id: docRef.id };
        } catch (error) {
            console.error('Erro ao salvar mensagem:', error);
            return { success: false, error };
        }
    },

    // Buscar todas as mensagens
    async getAllMessages() {
        try {
            const snapshot = await db.collection('contacts')
                .orderBy('createdAt', 'desc')
                .get();
            
            return snapshot.docs.map(doc => ({
                id: doc.id,
                ...doc.data()
            }));
        } catch (error) {
            console.error('Erro ao buscar mensagens:', error);
            return [];
        }
    }
};

// Funções para Upload de Arquivos
export const storageOperations = {
    // Upload de imagem
    async uploadImage(file, path) {
        try {
            const storageRef = storage.ref();
            const fileRef = storageRef.child(path);
            await fileRef.put(file);
            const url = await fileRef.getDownloadURL();
            return { success: true, url };
        } catch (error) {
            console.error('Erro ao fazer upload:', error);
            return { success: false, error };
        }
    },

    // Deletar imagem
    async deleteImage(path) {
        try {
            const storageRef = storage.ref();
            const fileRef = storageRef.child(path);
            await fileRef.delete();
            return { success: true };
        } catch (error) {
            console.error('Erro ao deletar imagem:', error);
            return { success: false, error };
        }
    }
};

// Funções de Autenticação
export const authOperations = {
    // Login
    async login(email, password) {
        try {
            const userCredential = await auth.signInWithEmailAndPassword(email, password);
            return { success: true, user: userCredential.user };
        } catch (error) {
            console.error('Erro no login:', error);
            return { success: false, error };
        }
    },

    // Logout
    async logout() {
        try {
            await auth.signOut();
            return { success: true };
        } catch (error) {
            console.error('Erro no logout:', error);
            return { success: false, error };
        }
    },

    // Verificar estado da autenticação
    onAuthStateChanged(callback) {
        return auth.onAuthStateChanged(callback);
    }
}; 