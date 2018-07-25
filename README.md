# Image-Downsampling
We develop a new algorithm for image downsampling by employing a well-known method in psychology known as reverse correlation.

The project consists of 3 experiments:
1. Generating Image Dataset: First, we create a web interface for generating images using reverse correlation. For each full-sized image in our dataset, we generate a downsampled version of the image using a standard method (for example, Lanczos). We then add random noise to parts of the image and create pairs out of these random noise-added downsized images. We then ask the user to select the image which better resembles the original one. After receiving about 300 responses for each image, we take the average of the users' choices and these constitute our labels for the image dataset.

2. Comparing Reverse Correlation with Standard Methods: We compare a randomly chosen subset of the images obtained from experiment 1 with those obtained from existing standard methods for image downsizing - DPID, Pixelated Abstraction and Lanczos. We found that for 70% of the comapred images, our method achieves better performance than the other methods.

3. Learning an Algorithm to Generate Reverse Correlation Images: We train a cGAN to learn the mapping from full-sized images to that obtained by reverse correlation.


References:
A. Reverse Correlation:
1. L. Brinkman, A. Todorov & R. Dotsch (2017) Visualising mental representations: A primer on noise-based reverse correlation in social psychology, European Review of Social Psychology, 28:1, 333-361, https://doi.org/10.1080/10463283.2017.1381469

2. Richard F.Murray & Jason M.Gold (2004) Troubles with bubbles, https://doi.org/10.1016/j.visres.2003.10.006



B. 
